## ForceTemplate

MODx Revolution plugin which force use of given template for all newly created resources in given parents.

After installation use System settings forcetemplate namespace to set preferable settings values. 


### Available system settings (namespace forcetemplate):

* rules - rule of usage in format "parent_id:template" delimited by pipe (|) or double pipe (||). Example: 3:1|4:2.
* parents_check - check parents ids in rule for duplication with log error if yes. Default false.
* template_check - check template in rule for exists with log error if not. Default false.