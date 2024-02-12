# kawabunga

![Kawabunga](./docs/kawabunga.png)

Static PHP project where you can deploy an automatic HTTP REST API based on any MySQL database with several features.

## Installation

Download the project (unzipped) and place it wherever you want in your static PHP server.

From command line, you can use git as follows to integrate kawabunga in the current directory:

```sh
git clone https://github.com/allnulled/kawabunga.git .
```

Once you have downloaded the source of the project, you should configure the credentials of the database, on the first lines of the `kawabunga.php` file.

For now, it is all, as it has no dependencies.

## Usage 

Once you set kawabunga source code in one directory and configured the credentials for the database, it already works: that is the magic of static PHP.

You can use as much kawabunga instances on your server as you want, they will not collide as soon as you respect the directories of each instance.

Below are described the key points to understand how to use kawabunga.

### 1. Unique endpoint

Kawabunga reserves 1 directory as namespace. This is because it uses the `index.php` for all the operations it can execute.

Once you place the project in an endpoint, you can start making requests to its `index.php`.

### 2. Multiple operations

The `operation` parameter tells the server which operation wants the user to commit.

Below are explained the groups of operations supported automatically by kawabunga.

#### 2.1. Supports REST operations

The REST operations are the following:

   - `schema`. No parameters.
   - `select`. Accepts:
      - `table` as a `String`. **Required**.
      - `where` as an `Array<Array<String,String[,String]?>>`. **Optional**. A list of items with the form of: column, operation, complement. Where operation can be: =, !=, <, >, <=, >=, LIKE, NOT LIKE, IN, NOT IN, IS NUL, IS NOT NULL.
      - `sort` as an `Array<Array<String,String>>`. **Optional**. A list of items with the form of: column, direction. Where direction can be ASC or DESC.
      - `page` as an `Integer`. **Optional**. If 0, all items are returned. Defaults to 20, though.
   - `insert`
      - `table` as a `String`. **Required**.
      - `value` as an `Object`. **Required**.
   - `update`
      - `table` as a `String`. **Required**.
      - `id` as an `Integer`. **Required**. The id of the item.
      - `value` as an `Object`. **Required**. The values to be set.
   - `delete`
      - `table` as a `String`. **Required**.
      - `id` as an `Integer`. **Required**. The id of the item.

#### 2.2. Supports AUTH operations

The AUTH operations are the following:

   - `register_account`
      - `name` as a `String`. **Required**.
      - `password` as a `String`. **Required**.
      - `email` as a `String`. **Required**.
   - `confirm_account`
      - `email` as a `String`. **Required**.
      - `confirmation_token` as a `String`. **Required**.
   - `login_session`
      - `email` as a `String`. **Required**.
      - `password` as a `String`. **Required**.
   - `refresh_session`
      - `authentication` as a `String`. **Required**.
   - `logout_session`
      - `authentication` as a `String`. **Required**.
   - `forgot_credentials`
      - `email` as a `String`. **Required**.
   - `recover_credentials`
      - `email` as a `String`. **Required**.
      - `recovery_token` as a `String`. **Required**.
   - `change_password`
      - `name` as a `String`. **Required**.
      - `password` as a `String`. **Required**.
      - `password_confirmation` as a `String`. **Required**.
   - `unregister_account`
      - `name` as a `String`. **Required**.
      - `password` as a `String`. **Required**.
      - `password_confirmation` as a `String`. **Required**.

### 3. Requires some basic auth tables

For kawabunga to work properly with the authentication and authorization system, which is optional to use. you need to have on your database some tables. These are the tables:

- kw_users_to_confirm
- kw_users
- kw_groups
- kw_permissions
- kw_users_and_groups
- kw_groups_and_permissions
- kw_sessions

The tables are prefixed with `kw_` which stands for kawabunga.

The SQL scripts to CREATE, INSERT, POPULATE and ELIMINATE the required auth tables can be all found at the same folder, at:
  
  - [scripts/database/001.auth/creation.sql]([./scripts/database/001.auth/creation.sql)
  - [scripts/database/001.auth/insertion.sql]([./scripts/database/001.auth/insertion.sql)
  - [scripts/database/001.auth/population.sql]([./scripts/database/001.auth/population.sql)
  - [scripts/database/001.auth/elimination.sql]([./scripts/database/001.auth/elimination.sql)

You can use them to set up or reset your database. The `population.sql` is for you to insert the new registries adapted to your new application needs. The `insertion.sql`, instead, is the minimum registries for the application to be fully accessible at least to one user, the classical `admin@admin` by default. 

Remember, obviously, to change the password once you have an active kawabunga, in production at least.

### 4. New tables must have always an id

The good thing of kawabunga is that it lets you create your own schema, and set up and automatic API based on your specific data model needs.

But to be compatible with the way kawabunga works, there are some requirements. And this is the first one: all tables must provide an `id` column as primary key and an auto_increment field.

### 5. Relation tables must follow a nomenclature

There is one way, for future features to work properly, of declaring n-to-n relation tables. This is it:

- The name of the table must follow the pattern: `{first_table}_and_{second_table}`
- The table must contain the proper id primary key auto_increment (always, any table)
- The table must contain 2 added columns:
   - `id_{first_table}`
   - `id_{second_table}`

This way, you ensure your database will follow a comprehensible nomenclature for future releases to integrate future features. For now, it is just a recommendation.

