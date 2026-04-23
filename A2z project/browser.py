import sys
import os
import ctypes 
from PyQt6.QtCore import *
from PyQt6.QtWidgets import *
from PyQt6.QtWebEngineWidgets import *
from PyQt6.QtWebEngineCore import QWebEngineProfile
from PyQt6.QtGui import QFont, QIcon, QShortcut, QKeySequence

try:
    myappid = 'mycompany.a2zbrowser.v1' 
    ctypes.windll.shell32.SetCurrentProcessExplicitAppUserModelID(myappid)
except:
    pass

class A2ZBrowser(QMainWindow):
    def __init__(self):
        super().__init__()
        self.setWindowTitle("A2Z Browser")
        self.showMaximized()
    

        storage_path = os.path.join(os.path.dirname(os.path.abspath(__file__)), "browser_data")
        if not os.path.exists(storage_path):
            os.makedirs(storage_path)
            
        profile = QWebEngineProfile.defaultProfile()
        profile.setPersistentStoragePath(storage_path)
        profile.setPersistentCookiesPolicy(QWebEngineProfile.PersistentCookiesPolicy.AllowPersistentCookies)

        if getattr(sys, 'frozen', False):
            basedir = sys._MEIPASS
        else:
            basedir = os.path.dirname(os.path.abspath(__file__))
        
        icon_path = os.path.join(basedir, "a2z.ico")
        if os.path.exists(icon_path):
            self.setWindowIcon(QIcon(icon_path))

        self.setStyleSheet("""
            QMainWindow { background-color: #0d1117; }
            QToolBar { background-color: #161b22; border-bottom: 1px solid #30363d; padding: 5px; spacing: 10px; }
            #BookmarkBar { background-color: #0d1117; border-bottom: 1px solid #30363d; min-height: 35px; }
            QLineEdit { background-color: #010409; color: #58a6ff; border: 1px solid #30363d; border-radius: 10px; padding: 6px 15px; font-size: 14px; }
            QPushButton { background-color: #21262d; color: white; border-radius: 6px; padding: 5px 12px; font-weight: bold; }
            QPushButton:hover { background-color: #30363d; }
            QTabBar::tab { background: #161b22; color: #8b949e; padding: 10px 20px; border-right: 1px solid #0d1117; min-width: 150px; }
            QTabBar::tab:selected { background: #0d1117; color: #58a6ff; border-bottom: 2px solid #58a6ff; }
            QTabBar::close-button { image: url(close.png); subcontrol-position: right; }
        """)

        main_widget = QWidget()
        self.setCentralWidget(main_widget)
        self.layout = QVBoxLayout(main_widget)
        self.layout.setContentsMargins(0, 0, 0, 0)
        self.layout.setSpacing(0)

        self.nav_bar = QToolBar()
        self.layout.addWidget(self.nav_bar)
        
        self.add_nav_btn("‹", lambda: self.tabs.currentWidget().back())
        self.add_nav_btn("›", lambda: self.tabs.currentWidget().forward())
        self.add_nav_btn("↻", lambda: self.tabs.currentWidget().reload())
        
        self.url_bar = QLineEdit()
        self.url_bar.placeholderText = "ابحث أو اكتب رابطاً هنا..."
        self.url_bar.returnPressed.connect(self.navigate_to_url)
        self.nav_bar.addWidget(self.url_bar)
        
        self.star_btn = self.add_nav_btn("⭐", self.add_current_to_bookmarks)
        self.star_btn.setStyleSheet("color: #e3b341; font-size: 16px;")

        self.plus_btn = self.add_nav_btn("+", lambda: self.add_new_tab())
        self.plus_btn.setStyleSheet("background-color: #238636; color: white; font-size: 18px;")
        
        self.btn_menu = self.add_nav_btn("⋮", self.show_main_menu)

        self.bookmarks_bar = QToolBar("Bookmarks")
        self.bookmarks_bar.setObjectName("BookmarkBar")
        self.layout.addWidget(self.bookmarks_bar)

        self.tabs = QTabWidget()
        self.tabs.setDocumentMode(True)
        self.tabs.setTabsClosable(True)
        self.tabs.tabCloseRequested.connect(self.close_tab)
        self.tabs.currentChanged.connect(self.sync_url_with_tab)
        self.layout.addWidget(self.tabs)
        
        self.home_url = "http://a2zcompany.infinityfree.me/index.php"
        self.add_new_tab(QUrl(self.home_url), "A2Z Home")

        self.setup_shortcuts()

    def setup_shortcuts(self):
        QShortcut(QKeySequence("Ctrl+T"), self).activated.connect(lambda: self.add_new_tab())
        QShortcut(QKeySequence("Ctrl+W"), self).activated.connect(lambda: self.close_tab(self.tabs.currentIndex()))
        QShortcut(QKeySequence("Ctrl+R"), self).activated.connect(lambda: self.tabs.currentWidget().reload())
        QShortcut(QKeySequence("Ctrl+L"), self).activated.connect(self.url_bar.setFocus)

    def add_nav_btn(self, text, func):
        btn = QPushButton(text)
        btn.clicked.connect(func)
        self.nav_bar.addWidget(btn)
        return btn

    def add_new_tab(self, qurl=None, label="New Tab"):
        if qurl is None: qurl = QUrl(self.home_url)
        
        browser = QWebEngineView()
        browser.setUrl(qurl)
        
        idx = self.tabs.addTab(browser, label)
        self.tabs.setCurrentIndex(idx)
        
        browser.urlChanged.connect(lambda q: self.url_bar.setText(q.toString()) if browser == self.tabs.currentWidget() else None)
        browser.loadFinished.connect(lambda: self.tabs.setTabText(self.tabs.indexOf(browser), browser.page().title()[:15]))

    def add_current_to_bookmarks(self):
        browser = self.tabs.currentWidget()
        if browser:
            url = browser.url().toString()
            title = browser.page().title()[:12]
            self.create_bookmark_btn(title, url)

    def create_bookmark_btn(self, name, url):
        btn = QPushButton(name)
        btn.setStyleSheet("background: transparent; border: none; color: #8b949e; padding: 5px 10px; font-weight: normal;")
        btn.clicked.connect(lambda: self.tabs.currentWidget().setUrl(QUrl(url)))
        self.bookmarks_bar.addWidget(btn)

    def navigate_to_url(self):
        url = self.url_bar.text()
        if "://" not in url:
            url = "https://" + url
        self.tabs.currentWidget().setUrl(QUrl(url))

    def sync_url_with_tab(self, i):
        if self.tabs.currentWidget():
            qurl = self.tabs.currentWidget().url()
            self.url_bar.setText(qurl.toString())

    def close_tab(self, i):
        if self.tabs.count() > 1:
            self.tabs.removeTab(i)

    def show_main_menu(self):
        menu = QMenu(self)
        menu.addAction("Settings", lambda: QMessageBox.information(self, "A2Z", "All data is saved online!"))
        menu.addAction("Exit", self.close)
        menu.exec(self.btn_menu.mapToGlobal(QPoint(0, self.btn_menu.height())))
        
if __name__ == "__main__":
    app = QApplication(sys.argv)
    app.setFont(QFont("Segoe UI", 10))
    window = A2ZBrowser()
    window.show()
    sys.exit(app.exec())