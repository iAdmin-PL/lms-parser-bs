# LMS Bank Spoldzielczy parser

The parser recognizes and identifies transfer based on following schemats in CSV:
1) invoice number
2) customer's personal data
3) customer's ID

or you have posibility to manually bind transfers to a customer.

## Installation

1. Create folder `backups/import` and add correct permissions.  
2. Copy two files from `modules` and one from `templates`.
3. Add to `lib/menu.php` following lines in array `finances`.

```php
array(
    'name' => trans('Import BS'),
    'link' => '?m=cashimportbs',
    'tip' => trans('Import cash operations from BS'),
    'prio' => 115,
),
```

When will you finish the installation process, you will see updated menu with a new option **Import BS**, under Finances tab in you LMS.

## Usage
Usage is really simple:
1. Import BS CSV file.
2. The parser will take a. action
3. Verify found transfer and manually add unrecognized lines to a customer.
4. Import it!

## Contributing
1. Fork it!
2. Create your feature branch: `git checkout -b my-new-feature`
3. Commit your changes: `git commit -am 'Add some feature'`
4. Push to the branch: `git push origin my-new-feature`
5. Submit a pull request!
   
## History
Information about all changes are written in [CHANGELOG.md](CHANGELOG.md).

## License
Information about license is under [LICENSE](LICENSE).

## Info
The author is not responsible for possible errors, but comments can be made using `Issues` on GitHub.