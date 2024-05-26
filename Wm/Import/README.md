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
4. We can import new profile / file type into this command using below step
    1. Add new file type:
        1. No need to modify existing model class but make changes in di.xml file
        2. Add your new file extension in di.xml in "allowedExtensions"
            eq: excel
            <item name="excel" xsi:type="string">excel</item>
        3. Add your new module class in "allowedProfiles" with profile name for reading data in excel
            eq: <item name="sample-excel" xsi:type="object">Wm\Import\Model\Importer\Reader\ReadExcel</item>
        4. Give console command with new profile:
            eq: bin/magento customer:import sample-excel sample.excel
    2. Add differing columns:
        1. Add your column in di.xml mapper section
            eq. first_name
            <argument name="mappings" xsi:type="array">
                ...
                <item name="firstname" xsi:type="string">first_name</item>
                ...
            </argument>
