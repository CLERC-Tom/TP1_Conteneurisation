# TP1 : Conteneurisation avec Docker et Kubernetes

## Sommaire

- [Consigne](#consigne)
- [Choix techniques](#choix-techniques)
- [Comment exécuter](#comment-exécuter)
- [Répartition des tâches](#répartition-des-tâches)
- [Équipe](#équipe)

## Consigne

Objectif : comprendre les principes de la conteneurisation, comparer machines virtuelles et conteneurs, et déployer une application conteneurisée en appliquant les bonnes pratiques de sécurité.

Sujet : conteneurisation d'une application web simple, puis déploiement sur un cluster Kubernetes.

Tâches :

1. Créer un Dockerfile pour conteneuriser l'application.
2. Optimiser l'image (réduction de taille, utilisateur non-root, bonnes pratiques de sécurité).
3. Déployer l'application sur Kubernetes (Deployment, Service).
4. Configurer les ressources (CPU, mémoire) et la scalabilité (replicas).
5. Analyser les différences entre VM et conteneur (isolation, performance, sécurité).

## Choix techniques

- **Application** : page web statique (Hello World) dans `app/`, volontairement simple pour se concentrer sur la conteneurisation.
- **Serveur web** : nginx sur une image alpine, légère.
- **Sécurité de l'image** : `nginxinc/nginx-unprivileged`, qui tourne sans root et écoute sur le port 8080.
- **Docker Compose** : lancement local en une commande.
- **Kubernetes** : cluster local `kind`, piloté avec `kubectl`. Le Deployment fixe 3 replicas avec des `requests` et `limits` CPU et mémoire, et un `securityContext` non-root. Le Service expose le port 80 vers le port 8080 des pods.
- **CI** : GitHub Actions lance `docker compose up` à chaque push et pull request vers `main`. La branche `main` est protégée : le check `compose` doit être vert.
- **Portabilité** : `.gitattributes` force les fins de ligne LF pour que le projet fonctionne pareil sur Mac et Windows (Docker Desktop avec WSL2).

## Comment exécuter

Prérequis : Docker Desktop lancé, `kind` et `kubectl` installés.

### Avec Docker Compose

```
docker compose up -d --build
```

Le site est disponible sur http://localhost:8080. Pour arrêter :

```
docker compose down
```

### Avec Kubernetes

```
docker build -t tp1-hello:1.0 -f docker/Dockerfile .
kind create cluster --name tp1
kind load docker-image tp1-hello:1.0 --name tp1
kubectl apply -f k8s/
kubectl get pods
kubectl port-forward service/tp1-hello 8080:80
```

Le site est disponible sur http://localhost:8080. Pour changer le nombre de replicas :

```
kubectl scale deployment tp1-hello --replicas=5
```

Pour tout supprimer :

```
kind delete cluster --name tp1
```

## Répartition des tâches

Le détail et l'avancement sont dans [doc/repartition-des-taches.md](doc/repartition-des-taches.md).

## Équipe

Projet réalisé par Tom, Aymeric, Thomas, Antoine et Julie.
