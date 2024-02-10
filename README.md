# kawabunga

![Kawabunga](./docs/kawabunga.png)

Static PHP project where you can deploy an automatic HTTP REST API based on any MySQL database with several features.

## Installation

Download the project (unzipped) and place it wherever you want in your static PHP server.

## Usage 

Below are described the key points to understand to use kawabunga.

### Unique endpoint

Kawabunga reserves 1 directory as namespace. This is because it uses the `index.php` for all the operations it can execute.

Once you place the project in an endpoint, you can start making requests to its `index.php`.

### Multiple operations

The `operation` parameter tells the server which operation wants the user to commit.

Below are listed the available values for `operation` parameter, followed by the other available parameters in that operation:

   - `"schema"`. No parameters.
   - `"select"`. Accepts:
      - `table` as a `String`. **Required**.
      - `where` as an `Array<Array<String,String[,String]?>>`. **Optional**.
      - `sort` as an `Array<Array<String,String>>`. **Optional**.
      - `page` as an `Integer`. **Optional**.
   - `"insert"`
      - `table` as a `String`. **Required**.
      - `value` as an `Object`. **Required**.
   - `"update"`
      - `table` as a `String`. **Required**.
      - `id` as an `Integer`. **Required**.
      - `value` as an `Object`. **Required**.
   - `"delete"`
      - `table` as a `String`. **Required**.
      - `id` as an `Integer`. **Required**.
   - `"register_account"`
      - `name` as a `String`. **Required**.
      - `password` as a `String`. **Required**.
      - `email` as a `String`. **Required**.
   - `"confirm_account"`
      - `email` as a `String`. **Required**.
      - `confirmation_token` as a `String`. **Required**.
   - `"login_session"`
      - `email` as a `String`. **Required**.
      - `password` as a `String`. **Required**.
   - `"refresh_session"`
      - `authentication` as a `String`. **Required**.
   - `"logout_session"`
      - `authentication` as a `String`. **Required**.
   - `"forgot_credentials"`
      - `email` as a `String`. **Required**.
   - `"recover_credentials"`
      - `email` as a `String`. **Required**.
      - `recovery_token` as a `String`. **Required**.
   - `"change_password"`
      - `name` as a `String`. **Required**.
      - `password` as a `String`. **Required**.
      - `password_confirmation` as a `String`. **Required**.
   - `"unregister_account"`
      - `name` as a `String`. **Required**.
      - `password` as a `String`. **Required**.
      - `password_confirmation` as a `String`. **Required**.

