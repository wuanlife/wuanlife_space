// lazyload router
/*module.exports = (file) => {
    return resolve => require(['views/' + file + '.vue'], resolve)
}
*/

module.exports = file => () => import('views/' + file + '.vue')