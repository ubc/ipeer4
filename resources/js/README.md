Directories:

* store - pinia stores
* plugin - configuration/definition for vue plugins or other external libraries
  * router - vue-router configuration here
* page - intended to be components that are only used once by the router
* component - intended to be components that may be used in many places

Custom globals made available to all Vue components in app.js:

* ErrorBox - For any forms that need to display errors, they need to put this above the primary submit button.
* $error - ErrorStore, for reporting/clearing errors
* $axios - for making http requests

User-facing components should expect to handle exceptions, as we want to
customize error presentation to the user. This means that our Pinia stores don't
need to explicitly catch exceptions, as we can let the user-facing components
handle them.
