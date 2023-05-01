import { Notify } from 'quasar'

/**
 * Consolidate common toast notifications to a single place.
 *
 * Toast notifications are expected to be short concise messages that'll only
 * appear temporarily. It should not be used for messages that should be
 * persistent to some degree.
 */

const notify = {
  ok(msg) {
    Notify.create({
      progress: true, // show timeout bar
      closeBtn: true, // show close button
      type: 'positive',
      message: msg
    })
  },
  err(msg) {
    Notify.create({
      progress: true,
      closeBtn: true,
      type: 'negative',
      message: msg
    })
  }
}

export default notify
