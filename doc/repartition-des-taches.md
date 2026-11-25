# Répartition des tâches

| Tâche | Intitulé | Responsable | Statut |
|-------|----------|-------------|--------|
| 1 | Dockerfile | Tom et Thomas | Fait |
| 2 | Optimiser et sécuriser l'image | Thomas | Fait |
| 3 | Déployer sur Kubernetes | Aymeric | Fait |
| 4 | Ressources et scalabilité | Antoine | Fait |
| 5 | Analyse VM contre conteneur | Julie | Fait |
| 6 | Rendu | Tom | En cours |

Statuts : À faire, En cours, Fait. Mettre à jour la colonne dès qu'une tâche change d'état.

## Tâche 1 : Dockerfile (Tom et Thomas)

- [x] Écrire `docker/Dockerfile` : base nginx, copie de `app/`, port 8080
- [x] Tester avec `docker build` puis `docker run`

## Tâche 2 : Optimiser et sécuriser l'image (Thomas)

- [x] Base légère (alpine) et utilisateur non-root (`nginxinc/nginx-unprivileged`)
- [x] `.dockerignore`, pas de secrets dans l'image
- [x] Mesurer la taille avant et après : 261 Mo (`nginx:latest`) contre 91,3 Mo
- [x] Vérifier le non-root avec `docker run --rm <image> id`

## Tâche 3 : Déployer sur Kubernetes (Aymeric)

- [x] Installer `kind` et `kubectl` (voir `requirements.txt`)
- [x] Créer le cluster
- [x] `kubectl apply -f k8s/deployment.yaml -f k8s/service.yaml` et vérifier que les pods sont `Running`

## Tâche 4 : Ressources et scalabilité (Antoine)

- [x] `requests` et `limits` CPU et mémoire, 3 replicas
- [x] `kubectl describe deployment` pour montrer les limites

## Tâche 5 : Analyse VM contre conteneur (Julie)

- [x] Analyse dans `doc/analyse VM contre conteneur.md`

## Tâche 6 : Rendu (Tom)

- [x] Captures et résultats des tâches 1 à 3 dans le README
- [ ] CI verte
- [ ] Dernière vérification que tout se lance sur Windows

## TP5 : Sécurisation de Kubernetes et du pipeline (Tom)

- [x] RBAC, Network Policy, Pod Security Standards dans `k8s/securite/`
- [x] Secret hors manifeste, chiffrement etcd et journal d'audit (`kind/`)
- [x] Scans Trivy et Checkov, 3 corrections ou plus (`doc/securite/`)
- [x] Pipeline `.github/workflows/securite.yml`
- [x] Synthèse des risques résiduels
