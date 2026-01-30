# INSTALL.md (Version Développeur)

*Guide complet pour configurer, développer, tester et déployer le projet (nginx + PHP-FPM + MariaDB) en Docker, Balena Cloud ou Kubernetes.*

---

## 🛠️ Prérequis

### ✅ Obligatoires (pour tous les environnements)


| Outil              | Version recommandée | Vérification               | Lien                                                    |
| ------------------ | ------------------- | -------------------------- | ------------------------------------------------------- |
| **Docker CE**      | ≥ 20.10             | `docker --version`         | [Installer Docker](https://docs.docker.com/get-docker/) |
| **Docker Compose** | ≥ 2.0               | `docker-compose --version` | Inclus avec Docker Desktop                              |
| **Git**            | ≥ 2.30              | `git --version`            | [Installer Git](https://git-scm.com/downloads)          |
| **Bash**           | ≥ 5.0               | `bash --version`           | Terminal Linux/macOS                                    |


### ⚠️ Optionnels (selon l'environnement cible)


| Outil                 | Nécessaire pour                                | Installation                                                 |
| --------------------- | ---------------------------------------------- | ------------------------------------------------------------ |
| **Node.js + Yarn**    | Déploiement Balena Cloud (`balena-cloud-apps`) | `npm install -g yarn`                                        |
| **Balena CLI**        | Déploiement sur Balena Cloud                   | `npm install -g balena-cli`                                  |
| **Kubectl + Kompose** | Déploiement Kubernetes                         | [Installer kubectl](https://kubernetes.io/docs/tasks/tools/) |


> **⚡ Note** : Pour du **développement local pur**, seul **Docker + Bash** sont nécessaires.

---

## 🚀 Installation et Configuration Initiale

### 1️⃣ Cloner le dépôt

```bash
git clone <URL_DU_DEPOT>
cd <DOSSIER_DU_PROJET>
```

### 2️⃣ Initialiser l'environnement

#### Pour du développement local (Docker)

```bash
# Configurer les variables et la base de données
./configure.sh --docker --mig-database -u -i

# Lancer les conteneurs
docker-compose up -d
```

> **Options de `configure.sh**` :
>
> - `--docker` : Configure pour Docker local.
> - `--mig-database` : Applique les migrations MySQL.
> - `-u` : Met à jour les dépendances.
> - `-i` : Installe les paquets manquants.

#### Pour le déploiement Balena Cloud

```bash
# Installer les dépendances Node.js (si balena-cloud-apps est utilisé)
yarn install

# Ajouter balena-cloud-apps (si non déjà installé)
yarn add balena-cloud-apps

# Exporter le chemin des binaires localement (optionnel)
export PATH="./node_modules/.bin:$PATH"
```

---

## ⚙️ Configuration du Projet

### 📂 Fichiers de configuration principaux


| Fichier                                    | Description                                       | Exemple                                                           |
| ------------------------------------------ | ------------------------------------------------- | ----------------------------------------------------------------- |
| `docker-compose.yml`                       | Définition des services (nginx, PHP-FPM, MariaDB) | [Voir la doc Docker](https://docs.docker.com/compose/)            |
| `balena.yml`                               | Configuration pour Balena Cloud                   | [Doc Balena](https://www.balena.io/docs/learn/deploy/deployment/) |
| `common.env`                               | Variables communes à toutes les architectures     | `BALENA_PROJECTS=(.)`                                             |
| `armhf.env` / `aarch64.env` / `x86_64.env` | Variables spécifiques à l'architecture            | `BALENA_ARCH=armhf`                                               |


---

### 🔐 Variables d'Environnement Essentielles

#### Base de Données (MariaDB/MySQL)

```ini
# common.env ou <arch>.env
MYSQL_DATABASE=aria_db
MYSQL_USER=maria
MYSQL_USER_PASSWORD=Some-robust-Password
MYSQL_ROOT_PASSWORD=SoMe-MorE-Robust-PAssWOrd!
MYSQL_BIND_ADDRESS=0.0.0.0
MYSQL_TCP_PORT=3306
```

#### Sécurité (CakePHP)

```ini
CAKEPHP_SECRET_TOKEN=your_random_token_here
CAKEPHP_SECRET_SALT=your_random_salt_here
CAKEPHP_SECURITY_CIPHER_SEED=your_cipher_seed_here
```

> **⚠️ Générer des valeurs sécurisées** :
>
> ```bash
> # Pour un mot de passe hashé (MASTER_PASSWORD_HASH)
> ./configure.sh -p "votre_mot_de_passe" -s hash
>
> # Ou via PHP (si disponible)
> php -r 'echo password_hash("votre_mot_de_passe", PASSWORD_DEFAULT);'
> ```

#### Serveur Web (nginx/Apache)

```ini
SERVER_NAME=www-machine.local  # ou votre domaine
PGID=0                         # ID du groupe (0 = root)
PUID=0                         # ID de l'utilisateur (0 = root)
TZ=Europe/Paris                # Timezone
```

---

## 💻 Développement Local

### Lancer le projet

```bash
# Démarrer tous les services
docker-compose up -d

# Accéder aux logs
docker-compose logs -f

# Arrêter les services
docker-compose down
```

### Tester l'application

1. **Valider la configuration** :
  ```bash
   ./configure.sh --docker --mig-database -u -i
  ```
2. **Exécuter les tests** :
  ```bash
   ./test-cake.sh --docker
  ```
3. **Accéder à l'application** :
  - URL : `http://localhost` ou `http://${SERVER_NAME}`
  - Admin : `/admin/index.php` (utilisez `MASTER_PASSWORD_HASH`)

---




| &nbsp; | &nbsp; |
| ------ | ------ |
| &nbsp; | &nbsp; |
| &nbsp; | &nbsp; |
| &nbsp; | &nbsp; |
| &nbsp; | &nbsp; |


---





```bash
docker-compose up -d --build
```

> &nbsp;

---





1. **Installer les dépendances** :
  ```bash
   yarn install
   yarn add balena-cloud-apps  # Si non déjà présent
  ```
2. **Configurer les variables** :
  - Éditez `balena.yml` et `<arch>.env` (ex: `armhf.env` pour Raspberry Pi 32 bits).
  - Exemple de `balena.yml` :
    ```yaml
    name: mon-projet
    type: sw
    version: 1.0.0
    ```



```bash
# Pour ARM 32 bits (Raspberry Pi 3/4)
./deploy.sh armhf --balena

# Pour ARM 64 bits
./deploy.sh aarch64 --balena

# Pour x86_64 (PC)
./deploy.sh x86_64 --balena
```

> &nbsp;
>
> - `--balena` : Déploie sur Balena Cloud.
> - `--local` : Construit localement (pour tests).
> - `--push` : Force le push même en cas d'erreur de cross-build.
> - `--build-deps` : Rebuild les dépendances Docker.



&nbsp;

```
[Error] The command 'cross-build-start' returned a non-zero code: 1
```

&nbsp;

```bash
./deploy.sh aarch64 --balena --push
```



1. Forkez le dépôt.
2. Cliquez sur **"Deploy with Balena"** dans le README.
3. Sélectionnez une **fleet** et déployez.

---



1. **Convertir docker-compose en Kubernetes** :
  ```bash
   ./kompose.sh up
  ```
2. **Vérifier le déploiement** :
  ```bash
   kubectl get pods
   kubectl get services
  ```

> &nbsp;

---





1. Éditez `deployment/images/primary/Dockerfile.template`.
2. **Blocs de cross-build pour ARM** (Balena) :
  ```dockerfile
   # [ "cross-build-start" ]
   RUN apt-get update && apt-get install -y \
       build-essential \
       && rm -rf /var/lib/apt/lists/*
   # [ "cross-build-end" ]
  ```
3. **Rebuild l'image** :
  ```bash
   ./deploy.sh aarch64 --local --build-deps
  ```



```bash
# Mettre à jour la version (ex: 1.0.0 -> 1.0.1)
npm version patch  # ou minor/major

# Pousser les tags
git push --tags
git push
```

> &nbsp;

---




| &nbsp; | &nbsp; | &nbsp; |
| ------ | ------ | ------ |
| &nbsp; | &nbsp; | &nbsp; |
| &nbsp; | &nbsp; | &nbsp; |
| &nbsp; | &nbsp; | &nbsp; |
| &nbsp; | &nbsp; | &nbsp; |


> &nbsp;

---






| &nbsp; | &nbsp; | &nbsp; |
| ------ | ------ | ------ |
| &nbsp; | &nbsp; | &nbsp; |
| &nbsp; | &nbsp; | &nbsp; |
| &nbsp; | &nbsp; | &nbsp; |
| &nbsp; | &nbsp; | &nbsp; |
| &nbsp; | &nbsp; | &nbsp; |




```bash
# Voir les logs de tous les services
docker-compose logs -f

# Entrer dans un conteneur (ex: php-fpm)
docker-compose exec php-fpm bash

# Vérifier les variables d'environnement
docker-compose exec php-fpm env

# Lister les conteneurs en cours
docker ps

# Nettoyer les volumes et réseaux
docker system prune -a --volumes
```

---



1. **Forker** le dépôt sur GitHub.
2. **Créer une branche** pour votre fonctionnalité :
  ```bash
   git checkout -b feature/ma-fonctionnalité
  ```
3. **Commiter** vos changements :
  ```bash
   git commit -m "Ajout de ma fonctionnalité"
  ```
4. **Pousser** vers votre fork :
  ```bash
   git push origin feature/ma-fonctionnalité
  ```
5. **Ouvrir une Pull Request** sur le dépôt principal.

---



&nbsp;

&nbsp;

>