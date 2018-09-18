## ForceTemplate

MODx Revolution plugin which force sets given template for all newly created resources in given parents.

Component work with "Quick Create" menu, allow specify an id or full template name in rules and support nesting/multi-level usage.   
If parent duplicates in rules, the last rule will be applied. You can find duplications with parents_check option.
If non-existent template is given in the rule new document will get parent template and log notification will be generated.
If nesting level is not specified in the rule, nesting level will be 1, it means the current parent.

After installation use System settings forcetemplate namespace to set preferable settings values. 


### Available system settings (namespace forcetemplate):

* rules - rule of usage in format "parent_id:template:level" delimited by pipe (|) or double pipe (||). Example: 3:1|4:my template:5.
* quick_create - allow to turn on/off force set of given template to "Quick Create" menu in document tree. Default true.
* parents_check - check parents ids in rule for duplication with log error if yes. Default false.

### Usage example

Determine the desired parent and the level of nesting, if there is a desire to use it. The default nesting level is 1.
In System settings choose forcetemplate namespace and fill the field with rules (rules). For example, we need to set two rules:
* There is a parent with id 3 with 1-st level of nesting and a template with id 1, then you need to specify 3:1.
* There is a parent with id 4 with nesting level 5 and the template "my template" (lazy to look for id), then you need to specify: 4:my template:5.

To combine these rules and use them together, combine them via pipe (|) or double pipe (||).