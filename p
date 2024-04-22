pip install pyTelegramBotAPI
pip install selenium
pip install chromedriver-autoinstaller
import telebot
import time
from selenium import webdriver

# توکن ربات تلگرام
TOKEN = 'Y6857897922:AAGJEQOGpJRE5v3LPBbgFszPYWbDCJnf2wkOUR_BOT_TOKEN'

# لیست اکانت‌های تلگرام
accounts = [
    'mamimann',
    'account2_username',
    # اکانت‌های بیشتر اینجا اضافه شوند
]

# تابع برای ورود به ویدیو چت با استفاده از Selenium
def join_video_chat(username):
    try:
        driver = webdriver.Chrome(executable_path='/usr/bin/chromedriver')
        driver.get('video_chat_link')
        time.sleep(5)  # انتظار برای بارگذاری صفحه
        # ورود به حساب با نام کاربری
        username_input = driver.find_element_by_xpath('//*[@id="username"]')
        username_input.send_keys(username)
        # انتظار برای ورود به ویدیو چت
        time.sleep(5)
        driver.quit()
    except Exception as e:
        print(f"Error joining video chat: {e}")

# ایجاد ربات با استفاده از توکن
bot = telebot.TeleBot(TOKEN)

@bot.message_handler(commands=['start'])
def send_welcome(message):
    bot.reply_to(message, "سلام! برای شروع عملیات جوین به چت ویدیو /join را ارسال کنید.")

@bot.message_handler(commands=['join'])
def join_video_chats(message):
    try:
        for account in accounts:
            # ورود به ویدیو چت برای هر اکانت
            join_video_chat(account)
        bot.reply_to(message, "تمام اکانت‌ها با موفقیت به ویدیو چت پیوستند!")
    except Exception as e:
        bot.reply_to(message, f"خطا در ورود به ویدیو چت: {e}")

# اجرای ربات
bot.polling()
