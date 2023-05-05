# Frontend
## Notify/Toasts
Temporary and short messages to the user. Mostly to tell them if an operation
was successful or if it failed. This uses Quasar's 'Notify' plugin. Hence our
customization lives in `@/plugin/notify.js`.

This is located in Vue components as `this.$notify`, some examples:
```javascript
this.$notify.ok('User created!')
this.$notify.err('Failed to create user.')
```

Since these messages disappear after a few seconds, more detailed errors should
be placed in the ErrorStore as described in the [Error System](#error-system).

## Error System

Processes, stores, and displays errors. This contains two parts, the ErrorStore
proccesses and stores the errors, while the ErrorBox displays the errors.

* ErrorStore is a Pinia store in `@/store/ErrorStore.js`.
* ErrorBox is a Vue component in `@/component/ErrorBox.vue`.

To be specific, the ErrorStore currently handles Axios request errors. But it
can be changed to handle other errors if needed.

The ErrorStore is automatically cleared when the router change pages. This is
done via `@/App.vue` adding a navigation guard to the router.

Components will need to manually manage clearing errors though while the user
is on the same page. For most cases, where a page would have only one user
initiated request, this is easy since we just need to clear the error when a
new request is initiated and comes back. Something like:

```javascript
this.axios.post('/some/api', this.data).then((resp) => {
  this.$error.clear()
}).catch((err) => {
  this.$error.clear()
  this.$error.handle(err)
})
```
