The Wm\Import module has a console command that will import the customers from given file

Steps to import a customer:
1. add a csv / json file at magento root.
2. run command: bin/magento customer:import <profile-name> <source>
eq: bin/magento customer:import sample-csv sample.csv

NOTES:
1. Customers will be imported for default website (base) with website id 1.
2. Customers will be imported for customer group general with group id 1.
3. If customer email already exists for default website,
   importing will skip that customer
   and logs the error