# WP-PRO-QUIZ Questions/Answers Exporter

This PHP script reads questions and answers from a WordPress installation using the "WP-PRO-QUIZ" plugin and saves them in a .sql file for further use.

## Requirements

- PHP 5.6 or higher
- A WordPress installation with the "WP-PRO-QUIZ" plugin

## Usage

1. Ensure your WordPress installation is using the "WP-PRO-QUIZ" plugin.
2. Update ALL the database + WP information directly in the "create-dump.php" file.
3. Upload the PHP script and all files to your server.
4. Execute the script via command line or web browser.
5. The script will generate a .sql file containing all questions and answers.

## Note

- This script only extracts questions and answers from a WordPress installation using the "WP-PRO-QUIZ" plugin. It does not take responsibility for the use or modification of the data.
- Please also include the correct WP prefix in the "create-dump.php" file.
- **Security Warning:** The code has not been secured and should under no circumstances be used in public projects without modification/securing.

## License

This project is licensed under the [MIT License](LICENSE). For more information, see the LICENSE file.

## Contact

For questions, issues, or feedback, feel free to contact me at heiko@team-schoenefeld.de.
