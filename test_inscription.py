import logging
import os
from datetime import datetime
from pathlib import Path

from selenium import webdriver
from selenium.common.exceptions import WebDriverException
from selenium.webdriver.common.by import By
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.support.ui import Select, WebDriverWait


logging.basicConfig(
    level=logging.INFO,
    format="%(asctime)s %(levelname)s %(message)s",
    datefmt="%H:%M:%S",
)

BASE_URL = os.environ.get("APP_URL", "http://localhost:8000")
WAIT_SECONDS = 10


def cleanup_test_products(driver, wait, designations) -> None:
    for designation in designations:
        driver.get(f"{BASE_URL}/index.php?action=products")
        rows = driver.find_elements(By.CSS_SELECTOR, ".data-table tbody tr")
        matching_rows = [row for row in rows if designation in row.text]
        if not matching_rows:
            continue

        matching_rows[0].find_element(By.CSS_SELECTOR, "button[type='submit']").click()
        driver.switch_to.alert.accept()
        wait.until(EC.text_to_be_present_in_element(
            (By.CLASS_NAME, "alert-success"),
            "Produit supprimé avec succès",
        ))
        logging.info("Produit de test supprimé : %s", designation)


def main() -> None:
    driver = None
    wait = None
    step = "Démarrage de Chrome"
    test_products = []

    try:
        driver = webdriver.Chrome()
        wait = WebDriverWait(driver, WAIT_SECONDS)

        suffix = datetime.now().strftime("%Y%m%d%H%M%S%f")
        login = f"selenium{suffix}"
        email = f"{login}@example.com"
        password = "Selenium123!"

        step = "Ouverture du formulaire d'inscription"
        logging.info("ÉTAPE 1/5 — %s", step)
        driver.get(f"{BASE_URL}/index.php?action=register")
        wait.until(EC.visibility_of_element_located((By.ID, "login")))

        step = "Création du compte"
        logging.info("ÉTAPE 2/5 — %s (%s)", step, email)
        driver.find_element(By.ID, "login").send_keys(login)
        driver.find_element(By.ID, "email").send_keys(email)
        driver.find_element(By.ID, "password").send_keys(password)
        driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()
        wait.until(EC.text_to_be_present_in_element(
            (By.CLASS_NAME, "alert-success"),
            "Compte créé avec succès",
        ))
        logging.info("Compte créé avec succès.")

        step = "Connexion avec le compte créé"
        logging.info("ÉTAPE 3/5 — %s", step)
        driver.find_element(By.LINK_TEXT, "Connectez-vous ici").click()
        wait.until(EC.visibility_of_element_located((By.ID, "login")))
        driver.find_element(By.ID, "login").send_keys(login)
        driver.find_element(By.ID, "password").send_keys(password)
        driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()
        wait.until(EC.text_to_be_present_in_element(
            (By.TAG_NAME, "h1"),
            "Tableau de bord",
        ))
        if login not in driver.find_element(By.TAG_NAME, "body").text:
            raise AssertionError("Le tableau de bord n'affiche pas l'utilisateur connecté.")
        logging.info("Connexion réussie, tableau de bord affiché.")

        step = "Création de deux produits de test"
        logging.info("ÉTAPE 4/5 — %s", step)
        driver.get(f"{BASE_URL}/index.php?action=product-create")
        wait.until(EC.visibility_of_element_located((By.ID, "type")))
        product_types = [
            option.get_attribute("value")
            for option in Select(driver.find_element(By.ID, "type")).options
            if option.get_attribute("value")
        ]
        if len(product_types) < 2:
            raise AssertionError("Il faut au moins deux types disponibles pour tester le filtre.")

        for index, product_type in enumerate(product_types[:2], start=1):
            designation = f"selenium-filtre-{suffix}-{index}"
            test_products.append(designation)
            driver.get(f"{BASE_URL}/index.php?action=product-create")
            wait.until(EC.visibility_of_element_located((By.ID, "type")))
            Select(driver.find_element(By.ID, "type")).select_by_value(product_type)
            driver.find_element(By.ID, "designation").send_keys(designation)
            driver.find_element(By.ID, "price").send_keys("12.50")
            driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()
            wait.until(EC.text_to_be_present_in_element(
                (By.CLASS_NAME, "alert-success"),
                "Produit créé avec succès",
            ))
            logging.info("Produit de test créé : %s (%s)", designation, product_type)

        step = "Vérification du filtre par type"
        logging.info("ÉTAPE 5/5 — %s", step)
        driver.get(f"{BASE_URL}/index.php?action=products")
        wait.until(EC.visibility_of_element_located((By.ID, "type-filter")))
        filter_select = Select(driver.find_element(By.ID, "type-filter"))
        filter_type = product_types[0]
        filter_select.select_by_value(filter_type)
        driver.find_element(By.CSS_SELECTOR, ".product-filter button[type='submit']").click()
        wait.until(EC.presence_of_element_located((By.CSS_SELECTOR, ".data-table tbody tr")))

        rows = driver.find_elements(By.CSS_SELECTOR, ".data-table tbody tr")
        visible_types = [row.find_elements(By.TAG_NAME, "td")[2].text for row in rows]
        visible_designations = [row.find_elements(By.TAG_NAME, "td")[3].text for row in rows]
        if not visible_types or any(value != filter_type for value in visible_types):
            raise AssertionError(
                f"Le filtre « {filter_type} » affiche des produits d'autres types : {visible_types}"
            )
        if test_products[0] not in visible_designations or test_products[1] in visible_designations:
            raise AssertionError(
                "Le résultat filtré ne contient pas uniquement le produit de test attendu : "
                f"{visible_designations}"
            )
        if Select(driver.find_element(By.ID, "type-filter")).first_selected_option.get_attribute("value") != filter_type:
            raise AssertionError("Le type sélectionné n'est pas conservé après filtrage.")
        logging.info("Filtre validé : seuls les produits de type « %s » sont affichés.", filter_type)

        driver.find_element(By.LINK_TEXT, "Réinitialiser").click()
        wait.until(EC.visibility_of_element_located((By.ID, "type-filter")))
        reset_designations = [
            row.find_elements(By.TAG_NAME, "td")[3].text
            for row in driver.find_elements(By.CSS_SELECTOR, ".data-table tbody tr")
        ]
        if any(designation not in reset_designations for designation in test_products):
            raise AssertionError("La réinitialisation du filtre ne réaffiche pas les deux produits.")
        logging.info("Réinitialisation validée : les deux produits de test sont de nouveau visibles.")

        step = "Nettoyage des produits de test et déconnexion"
        logging.info("Nettoyage des produits de test.")
        cleanup_test_products(driver, wait, test_products)

        driver.find_element(By.LINK_TEXT, "Déconnexion").click()
        wait.until(EC.text_to_be_present_in_element(
            (By.CLASS_NAME, "alert-success"),
            "déconnecté",
        ))
        logging.info("Déconnexion réussie.")
        logging.info("TEST RÉUSSI — inscription, connexion, filtre produits et déconnexion.")

    except Exception:
        logging.exception("ÉCHEC pendant l'étape : %s", step)
        if driver is not None:
            result_dir = Path(__file__).resolve().parent / "selenium-results"
            result_dir.mkdir(exist_ok=True)
            timestamp = datetime.now().strftime("%Y%m%d-%H%M%S")
            screenshot_path = result_dir / f"echec-{timestamp}.png"
            html_path = result_dir / f"echec-{timestamp}.html"

            logging.error("URL actuelle : %s", driver.current_url)
            logging.error("Titre de la page : %s", driver.title)
            for alert in driver.find_elements(By.CSS_SELECTOR, ".alert"):
                logging.error("Message affiché : %s", alert.text)

            try:
                if driver.save_screenshot(str(screenshot_path)):
                    logging.error("Capture d'écran : %s", screenshot_path)
            except WebDriverException:
                logging.exception("Impossible de prendre la capture d'écran.")

            try:
                html_path.write_text(driver.page_source, encoding="utf-8")
                logging.error("HTML de la page : %s", html_path)
            except OSError:
                logging.exception("Impossible d'enregistrer le HTML de la page.")
            if wait is not None and test_products:
                try:
                    cleanup_test_products(driver, wait, test_products)
                except Exception:
                    logging.exception("Le nettoyage des produits de test a échoué.")
        raise

    finally:
        if driver is not None:
            driver.quit()


if __name__ == "__main__":
    main()
