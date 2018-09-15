## ForceTemplate

MODx Revolution plugin which force use of given template for all newly created resources in given parents.

If parent duplicates in rules, the last rule will be applied. You can find duplications with parents_check option.
If non-existent template is given in the rule new document will get parent template and log notification will be generated.
If nesting level is not specified in the rule, nesting level will be 1, it means the current parent.

After installation use System settings forcetemplate namespace to set preferable settings values. 


### Available system settings (namespace forcetemplate):

* rules - rule of usage in format "parent_id:template:level" delimited by pipe (|) or double pipe (||). Example: 3:1|4:2:5.
* parents_check - check parents ids in rule for duplication with log error if yes. Default false.