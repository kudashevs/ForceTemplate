## ForceTemplate

MODx Revolution plugin which force use of given template for all newly created resources in given parents.

If parent duplicates in rules, the last rule will be applied. You can find duplications with parents_check option.
If non-existent template is given new document will get parent template and log notification will be generated.

After installation use System settings forcetemplate namespace to set preferable settings values. 


### Available system settings (namespace forcetemplate):

* rules - rule of usage in format "parent_id:template" delimited by pipe (|) or double pipe (||). Example: 3:1|4:2.
* parents_check - check parents ids in rule for duplication with log error if yes. Default false.