# Database Answers Update Script

## Overview

This PHP script is designed to update a collection of quiz questions stored in a MySQL database. It adds unique IDs to each answer within a JSON string and removes unnecessary properties (`html`, `sortString`, and `sortStringHtml`) to simplify the data structure for each question's set of answers.

## Features

- **Add Unique IDs**: Automatically assigns a unique ID to each answer for all questions in the database.
- **Remove Unnecessary Properties**: Cleans up the JSON data by removing properties that are not used, keeping the data structure clean and efficient.
- **JSON Data Handling**: Decodes and re-encodes JSON strings to update each question's answer set within the database.

## Security Notice

Please note that this script is **not intended for use in a live production environment**. It is provided for development and testing purposes only. Ensure to thoroughly test the script in a controlled environment before considering any use case that may affect real data.

## Requirements

- PHP 7.0 or newer.
- Access to a MySQL database containing your quiz questions.
- PDO extension enabled in PHP for database operations.

## Usage

1. **Backup Your Database**: Before running this script, ensure you have a complete backup of your database to prevent data loss.

2. **Configure Database Connection**: You must adjust the database connection settings within the script to match your environment. Replace the placeholders with your actual database connection details:

    ```php
    //
    // DB CONNECTION
    //
    $host = 'INSERT-HOST';
    $dbname = 'INSERT-DB-NAME';
    $username = 'INSERT-USER-NAME';
    $password = 'INSERT-PASSWORD';
    ```

3. **Run the Script**: Execute the script by running it from the command line or by accessing it through a web browser, depending on your setup. The script will process each question in the database and update the answers accordingly.

    ```bash
    php path/to/your/script.php
    ```

4. **Verify the Updates**: After the script has run, check your database to ensure that the answers for each question have been correctly updated with unique IDs and that unnecessary properties have been removed.

## Contributing

Feel free to fork this repository and submit pull requests to contribute to this project. Any improvements or suggestions are welcome!

## License

This project is licensed under the MIT License - see the LICENSE.md file for details.

## Acknowledgments

- Thanks to everyone who has contributed to this project.
- Special thanks to [PDO](https://www.php.net/manual/en/book.pdo.php) for making database interactions easier.
