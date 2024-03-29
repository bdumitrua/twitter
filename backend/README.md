## Общие правила

1. **Нейминг:**
   - Используйте camelCase или PascalCase для нейминга в коде.
   - Для данных в MySQL используйте другие соглашения нейминга.

2. **Типизация:**
   - Везде, где возможно, необходимо прописывать ожидаемые типы.
   - Всем методам добавляется PHPDoc. 

3. **Работа с данными:**
   - Используйте репозитории для всей работы с данными, за исключением консьюмеров.
   - Для работы с данными в Firebase используется FirebaseService.

4. **Тестирование нового функционала:**
   - Новый функционал должен покрываться тестами.

## Распределение ответственности

### Модели (Models)
- Отвечают за связь с таблицей в MySQL, отображение связей и конфигурацию для индексации в Elasticsearch (при необходимости).
- Выполняют действия при создании нового экземпляра (например, отправка метрик при создании нового твита).

### Контроллеры (Controllers)
- Обрабатывают HTTP-запросы по соответствующим путям.
- Отправляют данные в сервисы и обрабатывают их ответы.

### Сервисы (Services)
- Дополнительно валидируют данные (при необходимости).
- Взаимодействуют с данными из баз данных через репозитории.

### Репозитории (Repositories)
- Формируют повторяющиеся запросы.
- Работают с данными (MySQL, Firebase, Redis).
- Подгружают необходимые данные.

### Консьюмеры (Consumers)
- Выполняют фоновые задачи на основе новых сообщений в Kafka.
- Создаются для каждого топика, прослушивая его и выполняя определенную логику.

### ДТО (DTO)
- Создают объекты для передачи данных между частями программы.
- Используются для компановки данных перед отправкой в репозиторий или в сервисы.

### События (Events)
- Запускают процессы при возникновении событий.
- Используются для отправки уведомлений о событиях, инициируя связанную логику.

### Слушатели Событий (Listeners)
- Реагируют на события и выполняют соответствующие действия.
- Активируются при возникновении событий и обрабатывают данные.

### Реквесты (Requests)
- Автоматически валидируют данные HTTP-запросов.
- Создаются под каждый энд-поинт с необходимыми правилами валидации.

### Ресурсы (Resources)
- Компануют данные перед отправкой ответов.
- Используются в сервисах для формирования JSON-ответов и включают дополнительную логику.

### Роуты (Routes)
- Создают энд-поинты для взаимодействия с бэкендом.
- Отдельные файлы роутов для каждого контроллера подключаются в RouteServiceProvider.

## Команды и скрипты

Проект включает в себя ряд полезных команд и скриптов, облегчающих разработку и управление проектом.

### Команды Artisan

- **Создание нового модуля**
  - `php artisan make:module {name}`
  - **Описание**: Создает новый модуль с базовым набором файлов. Имя модуля задается параметром `{name}`.

- **Обновление базы данных**
  - `php artisan db:refresh`
  - **Описание**: Очищает данные из Firebase bucket, удаляет все данные локальной базы данных, заново выполняет все миграции и сидеры. Возвращает JWT для авторизации от первого пользователя (для удобства). Также очищает кэш в Redis.

### Скрипты

- **Очистка данных Kafka**
  - `clearKafka.sh`
  - **Описание**: Удаляет все данные из топиков Kafka, очищая их.

- **Очистка логов Supervisor**
  - `clearSVLogs.sh`
  - **Описание**: Удаляет все логи консьюмеров, отслеживаемые через Supervisor.

- **Запуск в Docker**
  - `start.sh`
  - **Описание**: Используется при запуске контейнера бэкенда в Docker, инициируя необходимые процессы для работы приложения.


## Модели

Каждая модель в проекте соответствует определенной таблице в базе данных:

- **AuthRegistration**
  - **Таблица:** `auth_registrations`
  - **Назначение:** Хранение данных регистрации пользователей.

- **AuthReset**
  - **Таблица:** `auth_resets`
  - **Назначение:** Управление сбросом пароля аккаунта.

- **Chat**
  - **Таблица:** `chats`
  - **Назначение:** Хранение списка участников чата.

- **ChatMessage**
  - **Таблица:** `сhat_messages`
  - **Назначение:** Связь чат-сообщение для поиска чата по сообщению.

- **HiddenChat**
  - **Таблица:** `hidden_chats`
  - **Назначение:** Скрытие ненужных чатов от пользователя.

- **DeviceToken**
  - **Таблица:** `device_tokens`
  - **Назначение:** Управление девайс токенами для уведомлений.

- **NotificationsSubscribtion**
  - **Таблица:** `notifications_subscribtions`
  - **Назначение:** Подписка на твиты других пользователей.

- **UserNotification**
  - **Таблица:** `user_notifications`
  - **Назначение:** Сохранение уведомлений для пользователя.

- **RecentSearch**
  - **Таблица:** `recent_searches`
  - **Назначение:** Управление недавним поиском пользователей.

- **Tweet**
  - **Таблица:** `tweets`
  - **Назначение:** Управление данными твитов.

- **TweetDraft**
  - **Таблица:** `tweet_drafts`
  - **Назначение:** Управление черновиками твитов.

- **TweetFavorite**
  - **Таблица:** `tweet_favorites`
  - **Назначение:** Управление избранными твитами.

- **TweetLike**
  - **Таблица:** `tweet_likes`
  - **Назначение:** Управление лайками твитов.

- **TweetNotice**
  - **Таблица:** `tweet_notices`
  - **Назначение:** Управление упоминаниями в твитах.

- **User**
  - **Таблица:** `users`
  - **Назначение:** Управление данными пользователей и аутентификацией.

- **UserGroup**
  - **Таблица:** `user_group`
  - **Назначение:** Управление личными группами пользователей.

- **UserGroupMember**
  - **Таблица:** `user_group_members`
  - **Назначение:** Хранение участников группы.

- **UsersList**
  - **Таблица:** `users_lists`
  - **Назначение:** Управление списками пользователей.

- **UsersListMember**
  - **Таблица:** `users_list_members`
  - **Назначение:** Хранение отслеживаемых пользователей.

- **UsersListSubscribtion**
  - **Таблица:** `users_list_subscribtions`
  - **Назначение:** Хранение подписчиков списков.

- **UserSubscribtion**
  - **Таблица:** `user_subscribtions`
  - **Назначение:** Хранение подписчиков пользователей.
