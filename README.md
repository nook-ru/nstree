# Библиотека citrus/nstree

Содержит класс `Citrus\NSTree\DataManager`, унаследованный от `Bitrix\Main\Entity\DataManager`, позволяющий работать с деревьями Nested Set через API ORM Битрикс.

Для работы с деревом необходимо создать класс-наследник от `Citrus\NSTree\DataManager`. 

Метод `getMap()` обязательно должен содержать поля:

* ID - Идентификатор записи, первичный ключ
* PARENT_ID - Идентификатор родительской записи
* LEFT_MARGIN - левый ключ
* RIGHT_MARGIN - правый ключ
* DEPTH_LEVEL - уровень вложенности
* SORT - сортировка

Дополнительно поддерживаютя поля:

* ACTIVE - флаг активности
* GLOBAL_ACTIVE - флаг активности всего узла, устанавливается автоматически

## Примеры

Пример ORM-класса находится в файле `lib/example.php`

### Добавление новой записи в корень дерева

```php
Citrus\NSTree\ExampleTable::add(
    array(
        'NAME' => 'ROOT ROW'
    )
);
```

### Добавление записи в существующую ветку

```php
Citrus\NSTree\ExampleTable::add(
    array(
        'PARENT_ID' => $parent_node_id,
        'NAME' => 'CHILD ROW'
    )
);
```

### Перемещение записи или целой ветки в новую ветку

```php
Citrus\NSTree\ExampleTable::update(
    $id,
    array(
        'PARENT_ID' => $new_parent_node_id
    )
);
```

### Получение всего упорядоченного дерева, начиная от корня

```php
$res = Citrus\NSTree\ExampleTable::getList(
    array(
        'select' => array(
            'ID',
            'NAME'
        ),
        'order' => array(
            'LEFT_MARGIN' => 'ASC'
        )
    )
);
```

### Получение только корневых элементов

```php
$res = Citrus\NSTree\ExampleTable::getList(
    array(
        'select' => array(
            'ID',
            'NAME'
        ),
        'filter' => array(
            '=DEPTH_LEVEL' => 1
        ),
        'order' => array(
            'LEFT_MARGIN' => 'ASC'
        )
    )
);
```

### Получение всех потомков конкретной ветки дерева

```php
$node = Citrus\NSTree\ExampleTable::getRow(
    array(
        'select' => array(
            'LEFT_MARGIN',
            'RIGHT_MARGIN'
        ),
        'filter' => array(
            '=ID' => $node_id
        )
    )
);
$res = Citrus\NSTree\ExampleTable::getList(
    array(
        'select' => array(
            'ID',
            'NAME'
        ),
        'filter' => array(
            '>LEFT_MARGIN' => $node['LEFT_MARGIN'],
            '<RIGHT_MARGIN' => $node['RIGHT_MARGIN']
        ),
        'order' => array(
            'LEFT_MARGIN' => 'ASC'
        )
    )
);
```

### Получение всех предков конкретной ветки дерева

```php
$node = Citrus\NSTree\ExampleTable::getRow(
    array(
        'select' => array(
            'LEFT_MARGIN',
            'RIGHT_MARGIN'
        ),
        'filter' => array(
            '=ID' => $node_id
        )
    )
);
$res = Citrus\NSTree\ExampleTable::getList(
    array(
        'select' => array(
            'ID',
            'NAME'
        ),
        'filter' => array(
            '<LEFT_MARGIN' => $node['LEFT_MARGIN'],
            '>RIGHT_MARGIN' => $node['RIGHT_MARGIN']
        ),
        'order' => array(
            'LEFT_MARGIN' => 'ASC'
        )
    )
);
```

## Транзакции

Во избежание разрушения структуры дерева при совместном доступе желательно блокировать таблицу на запись и откатывать изменения при возникновении ошибок.
Для этого служат методы **lockTable()** и **unlockTable()**

```php
$connection = Bitrix\Main\Application::getConnection();
Citrus\NSTree\ExampleTable::lockTable();
try {
    Citrus\NSTree\ExampleTable::add(
        array(
            'PARENT_ID' => $parent_node_id,
            'NAME' => 'CHILD ROW'
        )
    );
    $connection->commitTransaction();
} catch (\Exception $e) {
    $connection->rollbackTransaction();
    Citrus\NSTree\ExampleTable::unlockTable();
    echo($e->getMessage() . "\n");
}
```