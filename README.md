# TP1 : Conteneurisation avec Docker et Kubernetes

![CI](https://github.com/CLERC-Tom/TP1_Conteneurisation/actions/workflows/ci.yml/badge.svg)

Conteneurisation d'une page web statique (Hello World) avec nginx, optimisation et sécurisation de l'image, déploiement sur un cluster Kubernetes local, puis analyse comparative VM contre conteneur.

## Sommaire

- [Consigne](#consigne)
- [Organisation du dépôt](#organisation-du-dépôt)
- [Choix techniques](#choix-techniques)
- [Prérequis et exécution](#prérequis-et-exécution)
- [Comment tester](#comment-tester)
- [Tâche 1 : Dockerfile](#tâche-1--dockerfile)
- [Tâche 2 : Optimisation et sécurité](#tâche-2--optimisation-et-sécurité)
- [Tâche 3 : Déploiement Kubernetes](#tâche-3--déploiement-kubernetes)
- [Tâche 4 : Ressources et scalabilité](#tâche-4--ressources-et-scalabilité)
- [Tâche 5 : VM contre conteneur](#tâche-5--vm-contre-conteneur)
- [Répartition des tâches](#répartition-des-tâches)
- [Équipe](#équipe)

## Consigne

Objectif : comprendre les principes de la conteneurisation, comparer machines virtuelles et conteneurs, et déployer une application conteneurisée en appliquant les bonnes pratiques de sécurité.

| Tâche | Intitulé |
|-------|----------|
| 1 | Créer un Dockerfile pour conteneuriser l'application |
| 2 | Optimiser l'image (taille, utilisateur non-root, sécurité) |
| 3 | Déployer l'application sur Kubernetes (Deployment, Service) |
| 4 | Configurer les ressources (CPU, mémoire) et la scalabilité (replicas) |
| 5 | Analyser les différences entre VM et conteneur |

## Organisation du dépôt

```
.
├── app/                 page web (Hello World)
├── docker/Dockerfile    image de l'application
├── compose.yml          lancement local avec Docker Compose
├── .dockerignore        fichiers exclus du contexte de build
├── .gitignore           fichiers non suivis (.env)
├── k8s/                 manifests Kubernetes (Deployment, Service)
├── doc/                 analyse VM contre conteneur, répartition des tâches, captures
├── .github/workflows/   CI GitHub Actions
└── requirements.txt     outils à installer
```

## Choix techniques

| Choix | Raison |
|-------|--------|
| Page statique dans `app/` | Rester simple pour se concentrer sur la conteneurisation |
| `nginxinc/nginx-unprivileged:alpine` | Image légère, utilisateur non-root, port 8080 non privilégié |
| Docker Compose | Lancement local en une commande : construit l'image depuis le Dockerfile et durcit le conteneur |
| `kind` et `kubectl` | Cluster Kubernetes local dans Docker, identique sur Mac, Windows et Linux |
| GitHub Actions | `docker compose up` à chaque push et pull request vers `main` |
| Branche `main` protégée | Le check `compose` doit être vert avant tout merge |
| `.gitattributes` (fins de ligne LF) | Même comportement sur Mac et Windows |

## Prérequis et exécution

Les commandes d'installation de Docker, kubectl et kind pour Mac, Windows et Linux sont dans [requirements.txt](requirements.txt). Docker Desktop doit être lancé.

Toutes les commandes se lancent depuis la racine du dépôt.

| Étape | Commande |
|-------|----------|
| Construire l'image | `docker build -t tp1-hello:1.0 -f docker/Dockerfile .` |
| Lancer le conteneur | `docker run -d --rm --name tp1 -p 8080:8080 tp1-hello:1.0` |
| Arrêter le conteneur | `docker stop tp1` |
| Lancer avec Compose | `docker compose up -d --build --wait` |
| Arrêter Compose | `docker compose down` |
| Créer le cluster | `kind create cluster --name tp1` |
| Charger l'image dans le cluster | `kind load docker-image tp1-hello:1.0 --name tp1` |
| Déployer | `kubectl apply -f k8s/` |
| Attendre les pods | `kubectl wait --for=condition=available deployment/web-hello-world --timeout=120s` |
| Accéder au site | `kubectl port-forward service/serveur-web 8080:80` |
| Supprimer le cluster | `kind delete cluster --name tp1` |

Le site est disponible sur http://localhost:8080.

## Comment tester

Quatre étapes, dans l'ordre. Chaque étape indique ce qu'on doit voir si tout fonctionne.

### Étape 1 : installer les outils (requirements.txt)

`requirements.txt` n'est pas un fichier à exécuter : il contient les commandes à copier dans un terminal. Ouvrir le terminal, coller le bloc de son système, puis lancer les trois commandes de vérification.

| | Mac | Windows | Linux |
|---|-----|---------|-------|
| Terminal à ouvrir | Terminal | PowerShell en administrateur | Terminal |
| Installer Docker | `brew install --cask docker` | `winget install -e --id Docker.DockerDesktop` | `curl -fsSL https://get.docker.com \| sh` |
| Installer kubectl et kind | `brew install kubectl kind` | `winget install -e --id Kubernetes.kubectl` puis `winget install -e --id Kubernetes.kind` | commandes `curl` du fichier |
| Après l'installation | Lancer Docker Desktop | Lancer Docker Desktop, redémarrer PowerShell | `sudo usermod -aG docker $USER`, puis se reconnecter |

Les commandes complètes de chaque système sont dans [requirements.txt](requirements.txt).

Vérification, la même sur les trois systèmes :

```
docker version
kubectl version --client
kind version
```

Ce qu'on doit voir : `docker version` affiche une partie `Client` et une partie `Server`, les deux autres affichent un numéro de version. Si la partie `Server` manque, Docker Desktop n'est pas lancé.

### Étape 2 : récupérer le projet

```
git clone git@github.com:CLERC-Tom/TP1_Conteneurisation.git
cd TP1_Conteneurisation
```

### Étape 3 : tester Docker

| Commande | Ce qu'on doit voir |
|----------|--------------------|
| `docker build -t tp1-hello:1.0 -f docker/Dockerfile .` | Le build se termine sans erreur |
| `docker run --rm tp1-hello:1.0 id` | `uid=101(nginx)`, donc pas root |
| `docker compose up -d --build --wait` | L'image est construite, puis le conteneur passe à `Healthy` |
| `curl http://localhost:8080` (sous Windows : `curl.exe http://localhost:8080`) | La page `Hello World` |
| `docker compose down` | Le conteneur et le réseau sont supprimés |

### Étape 4 : tester Kubernetes

L'image `tp1-hello:1.0` doit exister (étape 3).

| Commande | Ce qu'on doit voir |
|----------|--------------------|
| `kind create cluster --name tp1` | `Set kubectl context to "kind-tp1"` |
| `kind load docker-image tp1-hello:1.0 --name tp1` | `Image ... loaded` |
| `kubectl apply -f k8s/` | Deployment et Service `created` |
| `kubectl wait --for=condition=available deployment/web-hello-world --timeout=120s` | `condition met` |
| `kubectl get pods` | 3 pods `1/1 Running` |
| `kubectl port-forward service/serveur-web 8080:80` puis, dans un second terminal, `curl http://localhost:8080` | La page `Hello World` |
| `kind delete cluster --name tp1` | Le cluster est supprimé |

## Tâche 1 : Dockerfile

Le fichier `docker/Dockerfile` fait trois instructions : base nginx non-root, copie de `app/`, port 8080.

```
FROM nginxinc/nginx-unprivileged:alpine

COPY app/ /usr/share/nginx/html/

EXPOSE 8080
```

Test avec `docker build` puis `docker run` : la page Hello World est servie sur le port 8080.

![docker run](doc/image/docker-run.png)

Le même résultat avec Docker Compose, le conteneur passe à l'état `Healthy` :

![docker compose](doc/image/compose-up.png)

Compose construit l'image `tp1-hello:1.0` depuis `docker/Dockerfile` (section `build:` de `compose.yml`), puis applique les protections de sécurité. Le dossier `app/` est monté en lecture seule.

## Tâche 2 : Optimisation et sécurité

### Taille de l'image

| Image | Taille | Utilisateur | Commentaire |
|-------|--------|-------------|-------------|
| `nginx:latest` | 261 Mo | root | Référence avant optimisation |
| `nginx:1.27-alpine` | 76,8 Mo | root | Légère, mais s'exécute en root |
| `tp1-hello:1.0` (notre image) | 91,3 Mo | non-root (uid 101) | Alpine et non-root |

Notre image est environ 65 % plus petite que `nginx:latest` (261 Mo contre 91,3 Mo). Elle pèse 14,5 Mo de plus que `nginx:1.27-alpine`, un surcoût accepté pour ne pas tourner en root.

![taille des images](doc/image/docker-build-taille.png)

### Bonnes pratiques de sécurité

| Pratique | Mise en oeuvre | Où |
|----------|----------------|----|
| Utilisateur non-root | `nginx-unprivileged`, uid 101 | Dockerfile |
| Port non privilégié | 8080 au lieu de 80 | Dockerfile, Compose |
| Pas de secrets dans l'image | `.dockerignore` exclut `.env`, `.git` et tout ce qui est hors `app/` | `.dockerignore` |
| Système de fichiers en lecture seule | `read_only: true`, `/tmp` en tmpfs de 16 Mo | `compose.yml` |
| Aucune capability Linux | `cap_drop: ALL` | `compose.yml` |
| Pas d'élévation de privilèges | `no-new-privileges:true` | `compose.yml` |
| Limites de ressources | 128 Mo de mémoire sans swap, 0,5 CPU | `compose.yml` |
| Réseau dédié | réseau `web` au lieu de `docker0` | `compose.yml` |

Preuve du non-root avec `docker run --rm tp1-hello:1.0 id` :

![non-root](doc/image/docker-non-root.png)

## Tâche 3 : Déploiement Kubernetes

Le cluster est un cluster local `kind`, piloté avec `kubectl`.

| Objet | Fichier | Caractéristiques |
|-------|---------|------------------|
| Deployment `web-hello-world` | `k8s/deployment.yaml` | image `tp1-hello:1.0` (construite par notre Dockerfile), `imagePullPolicy: IfNotPresent`, label `app: hello-world` |
| Service `serveur-web` | `k8s/service.yaml` | ClusterIP, port 80 vers le port 8080 des pods |

Les trois pods passent à `Running` et le Service répond via `kubectl port-forward` :

![pods et service](doc/image/k8s-pods-service.png)

Le cluster `kind` ne voit pas les images du Docker local : il faut d'abord la lui envoyer avec `kind load docker-image tp1-hello:1.0 --name tp1`. Ensuite `imagePullPolicy: IfNotPresent` lui dit d'utiliser cette copie au lieu de la chercher sur Internet. La page servie est notre page Hello World :

![réponse du service](doc/image/k8s-curl.png)

## Tâche 4 : Ressources et scalabilité

| Paramètre | Valeur |
|-----------|--------|
| Replicas | 3 |
| CPU demandé (requests) | 100m |
| Mémoire demandée (requests) | 64Mi |
| CPU maximum (limits) | 250m |
| Mémoire maximum (limits) | 128Mi |

Pour changer le nombre de replicas :

```
kubectl scale deployment web-hello-world --replicas=5
```

## Tâche 5 : VM contre conteneur

L'analyse complète est dans [doc/analyse VM contre conteneur.md](doc/analyse%20VM%20contre%20conteneur.md).

| Critère | Machine virtuelle | Conteneur |
|---------|-------------------|-----------|
| Isolation | Forte, par le matériel et l'hyperviseur | Logique, par le noyau partagé |
| Sécurité | Surface d'attaque étroite | Une faille du noyau expose l'hôte |
| Démarrage | Dizaines de secondes à minutes | Moins d'une seconde à quelques secondes |
| Poids | Plusieurs Go | Quelques dizaines de Mo |
| Portabilité | Liée à l'hyperviseur | Image OCI identique du poste au cloud |
| Compatibilité OS | Tout OS invité | Même noyau que l'hôte |

La VM reste la référence quand l'isolation prime. Le conteneur s'impose quand la vitesse, la densité et la portabilité priment.

## Répartition des tâches

| Tâche | Intitulé | Responsable |
|-------|----------|-------------|
| 1 | Dockerfile | Tom et Thomas |
| 2 | Optimiser et sécuriser l'image | Thomas |
| 3 | Déployer sur Kubernetes | Aymeric |
| 4 | Ressources et scalabilité | Antoine |
| 5 | Analyse VM contre conteneur | Julie |
| 6 | Rendu | Tom |

Le suivi détaillé est dans [doc/repartition-des-taches.md](doc/repartition-des-taches.md).

## Équipe

Projet réalisé par Tom, Aymeric, Thomas, Antoine et Julie.
