# Project: Maps Scraper Application
# Target Repository: https://github.com/asisec/maps-scraper

## SYSTEM RULES (STRICTLY ENFORCED)
1. Do not add comment lines (//, #, /* */ etc.) to the code. Use comment lines ONLY if explicitly requested, and specifically use the `TODO:` format when doing so.
2. All text visible to the user (UI, error messages, notifications, labels etc.) MUST be in proper and fluent Turkish. Do not use English or broken Turkish expressions.
3. In the rest of the code (variable names, function names, class names, file names, database collections etc.), ONLY use English. Do not write Turkish code identifiers.
4. Always provide the CURRENT AND COMPLETE version of the code. Do not provide old-new comparisons, snippets, or fragmented outputs. Write clean, simple, and production-level code.
5. For every feature, provide the exact `git` commands to create a proper branch (e.g., `feature/docker-setup`), commit, and push to the `asisec/maps-scraper` repository.

## Phase 1 Objectives: Docker, CI/CD and Base Initialization
We are building a web application that scrapes Google Maps business data within a circular radius (without using paid Google Maps API for data extraction) and allows exporting this data (XLSX, PDF, Image).
Tech Stack: PHP Laravel, AngularJS, MongoDB, Docker, CI/CD, Swagger UI.

### Tasks for this Prompt:
1. Generate the `docker-compose.yml` and necessary `Dockerfile`s to run:
   - A PHP Laravel container (Backend)
   - A MongoDB container (Database)
   - A Node/Nginx container for AngularJS (Frontend)
2. Generate the GitHub Actions CI/CD pipeline file (`.github/workflows/main.yml`) that will run basic tests and prepare the deployment build when pushed to the main branch.
3. Provide the initial Laravel and AngularJS folder structure setup commands.
4. Provide the exact Git commands to initialize the repo, create a branch named `feature/initial-docker-setup`, commit these files, and push to `origin feature/initial-docker-setup`.

Output the complete configuration files and the terminal commands required to execute this phase.