# Twitter Clone project

## Основные ссылки

-   **Репозиторий GitHub**: [https://github.com/bdumitrua/twitter](https://github.com/bdumitrua/twitter)
-   **Jira**: [https://madumey.atlassian.net](https://madumey.atlassian.net)
-   **Figma**: [Figma Design](https://www.figma.com/file/rvk73KT8BRtMBgbnUQ7bX8/Twitter-pages?type=design&node-id=4:1224&mode=design&t=mx5pPfALWQrweNjB-1)

## Стек технологий

### Front-end

-   **Основной стек**: TypeScript, React, Redux Toolkit
-   **Запросы данных**: React-Query
-   **Сборка**: Webpack

### Backend

-   **Основной стек**: PHP, Laravel
-   **Развертывание**: Docker, Docker-Compose, Nginx
-   **Базы данных**: MySQL
-   **Кэширование**: Redis
-   **Очереди**: Apache Kafka
-   **Поиск**: Elasticsearch
-   **Логирование**: Monolog, Files (Связка Elasticsearch + Kibana выключена на время разработки)
-   **Метрики**: Prometheus (Grafana выключена на время разработки)
-   **Электронная почта**: Mailtrap
-   **Уведомления/Сообщения**: Firebase

## Разработчики

-   Бэкенд: https://github.com/bdumitrua
-   Фронтенд:
    -   https://github.com/MattyK03
    -   https://github.com/karasique22

## Установка и запуск (Docker)

Для развертывания проекта выполните следующие шаги:

### Настройка окружения

1. **Создание файла .env**:
    - Скопируйте содержимое `.env.example` в новый файл `.env`. Все необходимые стандартные переменные окружения уже настроены.

### Сборка и запуск проекта

2. **Сборка контейнеров**:
    - Соберите проект командой: `docker-compose up --build -d`.

### Backend

3. **Создание таблиц базы данных**:
    - Выполните: `docker-compose exec backend php artisan migrate`.
4. **(Опционально) Заполнение базы данных фейковыми данными**:

    - Выполните: `docker-compose exec backend php artisan db:seed`.
    - Пароль для всех пользователей, созданных сидером, будет 'password'.

5. **Доступ к API**:
    - Используйте `localhost/api` или `localhost:8000/api` для доступа к API (коллекция путей для Postman находится в папке backend (PostmanCollection.json))

### Frontend

6. **Установка зависимостей**:
    - Выполните: `docker-compose exec frontend npm install`.
7. **Запуск frontend**:
    - Выполните: `docker-compose exec frontend npm run start`.

### Доступ к приложению

8. **Доступ к frontend**:
    - Откройте `localhost` в вашем браузере.

## (Для разработчиков) Дополнительные шаги конфигурации

Перед началом работы над проектом необходимо выполнить следующие дополнительные шаги конфигурации:

1. **Настройка отправки электронной почты**:
   - Вставьте данные для `MAIL_USERNAME` и `MAIL_PASSWORD` в файл `.env`. Эти данные можно найти в рабочем чате.

2. **Настройка Firebase для аутентификации**:
   - Скопируйте файл `firebase-auth.json` в папку `backend`.

3. **Настройка Firebase Storage**:
   - Установите индивидуальный `FIREBASE_STORAGE_BUCKET` в файле `.env`.
