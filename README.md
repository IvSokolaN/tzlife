Тестовое задание для компании N.

Описание задания находится в [TASK.md](https://github.com/IvSokolaN/tzlife/blob/main/TASK.md)

Для запуска можно использовать Sail

```bash
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan db:seed
```

# Эндпоинты

### Получение списка товаров
```
GET /api/catalog
```

Возможные варианты query параметров:
* `page` - номер страницы
* `per_page` - количество товаров на странице

### Создание заказа
```
POST /api/create-order
```

В теле запроса передать список id товаров и их количества.

```json
{
    "products": [
        {
            "id": 42,
            "quantity": 3
        },
        {
            "id": 50,
            "quantity": 4
        }
    ]
}
```

### Подтверждение заказа

```
POST /api/approve-order
```

В теле запроса передать id заказа и id пользователя. ID пользователя передается только в рамках ТЗ.

```json
{
    "order_id": 29,
    "user_id": 9
}
```
