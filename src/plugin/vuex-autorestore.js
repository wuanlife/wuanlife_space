import ls from 'utils/localStorage'
import copy from 'utils/copy'

export const createLSPlugin = function (lsKey, mappings, whiteList = []) {
  const k = lsKey
  return store => {
    store.subscribe((mutation, state) => {
      if (whiteList.findIndex(m => m === mutation.type) < 0) {
        const cd = Object.create(null)
        Object.keys(state).forEach(k => {
          if (mappings[k]) {
            cd.val = copy(state[k], mappings[k])
          }
        })
        if (cd.val.uid !== null) {
          ls.setItem(k, cd)
        }
      }
    })
  }
}
