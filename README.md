# mysql-connector
A connector that provide various method to connect and query a mysql database

## Connect

`connect($host, $db, $user, $pass)`

```php
// Connector
$connector = new mySQLConnector();

// Connection
if(!$connector->connect("localhost", "db_name", "root", "P455W0rD")) {
    echo $connector->getError();
}
```

## Select

`select($select_clause, $from_clause, $where_clause = '', $groupby_clause = '', $orderby_clause = '', $limit_clause = '')`

```php
if(!$connector->select('*', 'images', 'cover = 1', '', 'id_image desc', '0, 5')) {
    echo $connector->getError();
}

foreach($connector->getSelectedData() as $row) {
    echo str_replace("\n", "", print_r($row, true))."\n";
}
```

## Insert

`insert($table, $row) `

```php
$row = array();
$row['id_image'] = 50;
$row['id_product'] = 100;
$row['position'] = 1;
$row['cover'] = 1;

if(!$connector->insert('images', $row)) {
    echo $connector->getError();
}

echo $connector->getLastInsertID();
```

## Update

`update($table, $where_clause, $row) `

```php
$row = array();
$row['cover'] = 0;

if(!$connector->update('images', 'id_image = 50', $row)) {
    echo $connector->getError();
}
```

## Delete

`delete($table, $where_clause) `

```php
if(!$connector->delete('images', 'id_image = 50')) {
    echo $connector->getError();
}
```

## Query

`query($request)`

```php
if(!$connector->query('CREATE DATABASE brand_new_database')) {
    echo $connector->getError();
}
```

## Transaction

```php
$connector->start();
$connector->commit();
$connector->rollback();
```
