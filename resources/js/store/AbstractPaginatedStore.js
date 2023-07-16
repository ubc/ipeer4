import axios from '@/plugin/axios'

/**
 * Trying to abstract out common store functions, particularly the stuff
 * dealing with pagination since I was pretty much copying them for every store.
 *
 * @param extension - object containing module specific implementation for
 * state/getters/actions, since we merge extension in last, the extensions
 * take precedence and can override the generic implementation.
 */ 
export default function(extension) {
  return {
    state: () => ({
      filter: '',
      pagination: {
        sortBy:      'id',
        descending:  false,
        page:        1,
        rowsPerPage: 10,
        rowsNumber:  0, // total entries
      },
      page: [],
      ...extension.state
    }),

    getters: {
      ...extension.getters
    },

    actions: {
      async index(url) {
        const params = new URLSearchParams({
          page:       this.pagination.page,
          per_page:   this.pagination.rowsPerPage,
          sort_by:    this.pagination.sortBy,
          descending: this.pagination.descending,
          filter:     this.filter,
        })
        const urlExt = url + '?' + params.toString()
        const resp = await axios.get(urlExt)
        this.page = resp.data.data
        this.setPagination({
          page:        resp.data.current_page,
          rowsPerPage: resp.data.per_page,
          sortBy:      resp.data.sort_by,
          descending:  resp.data.descending,
          rowsNumber:  resp.data.total
        })
      },
      resetPagination() {
        this.page                   = []
        this.filter                 = ''
        this.pagination.sortBy      = 'id'
        this.pagination.descending  = false
        this.pagination.page        = 1
        this.pagination.rowsPerPage = 10
        this.pagination.rowsNumber  = 0
      },
      setFilter(filter) {
        this.filter = filter
      },
      setPagination(pagination) {
        this.pagination.page         = pagination.page
        this.pagination.rowsPerPage  = pagination.rowsPerPage
        this.pagination.sortBy       = pagination.sortBy
        this.pagination.descending   = pagination.descending
        if (pagination.rowsNumber)
          this.pagination.rowsNumber = pagination.rowsNumber
      },
      ...extension.actions
    },
  }
}
