import base64
import os
import sys
import time

from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.support.ui import Select, WebDriverWait
from selenium.webdriver.support import expected_conditions as EC


BASE_URL = "https://quizquestapp.com/pos-sim"
USERNAME = os.getenv("POS_USER", "").strip()
PASSWORD = os.getenv("POS_PASS", "").strip()


def fail(message: str) -> None:
    print(f"FAILED: {message}")
    sys.exit(1)


def build_driver() -> webdriver.Chrome:
    if not USERNAME or not PASSWORD:
        fail("POS_USER or POS_PASS environment variable is missing.")

    options = Options()
    options.add_argument("--start-maximized")
    options.add_argument("--ignore-certificate-errors")
    options.add_argument("--disable-popup-blocking")

    driver = webdriver.Chrome(options=options)

    # Inject Basic Auth header using Chrome DevTools Protocol
    auth_token = base64.b64encode(f"{USERNAME}:{PASSWORD}".encode("ascii")).decode("ascii")
    driver.execute_cdp_cmd("Network.enable", {})
    driver.execute_cdp_cmd(
        "Network.setExtraHTTPHeaders",
        {"headers": {"Authorization": f"Basic {auth_token}"}}
    )

    return driver


def assert_text_present(driver: webdriver.Chrome, text: str, page_name: str) -> None:
    body = driver.page_source
    if text not in body:
        fail(f"{page_name} does not contain expected text: {text}")
    print(f"PASS: {page_name} contains expected text: {text}")


def open_and_check(driver: webdriver.Chrome, url: str, expected_text: str, page_name: str) -> None:
    driver.get(url)
    WebDriverWait(driver, 15).until(
        EC.presence_of_element_located((By.TAG_NAME, "body"))
    )
    time.sleep(1)
    assert_text_present(driver, expected_text, page_name)


def main() -> None:
    driver = build_driver()

    try:
        print("Opening homepage...")
        open_and_check(driver, f"{BASE_URL}/index.php", "Simple POS Simulation", "index.php")

        print("Opening product page...")
        open_and_check(driver, f"{BASE_URL}/product.php", "Available Products", "product.php")

        print("Opening cart page...")
        open_and_check(driver, f"{BASE_URL}/cart.php", "Cart Items", "cart.php")

        print("Opening checkout page...")
        open_and_check(driver, f"{BASE_URL}/checkout.php", "Checkout Form", "checkout.php")

        # Submit checkout form
        print("Submitting checkout form...")
        customer_input = WebDriverWait(driver, 15).until(
            EC.presence_of_element_located((By.ID, "customer"))
        )
        customer_input.clear()
        customer_input.send_keys("Jenkins Test User")

        payment_select = Select(driver.find_element(By.ID, "payment_method"))
        payment_select.select_by_visible_text("Cash")

        submit_button = driver.find_element(By.CSS_SELECTOR, "button[type='submit']")
        submit_button.click()

        WebDriverWait(driver, 15).until(
            EC.presence_of_element_located((By.TAG_NAME, "body"))
        )
        time.sleep(1)

        assert_text_present(driver, "Checkout successful", "checkout.php POST result")
        print("SUCCESS: Selenium UI test passed")

    except Exception as exc:
        fail(str(exc))
    finally:
        driver.quit()


if __name__ == "__main__":
    main()