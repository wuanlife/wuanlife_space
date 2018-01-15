
export default function authRule(mockAdapter) {
  mockAdapter
  .onPost('/users/signin').reply(200, {
    id: '192',
    name: '梁王',
    mail: '443474713@qq.com',
    'Access-Token': 'dsjkvbeivleavmkmvksdnboifejvmsks'
  })
  .onPost('/users').reply(200, {
    id: '192',
    name: '梁王',
    mail: '443474713@qq.com',
    'Access-Token': 'dsjkvbeivleavmkmvksdnboifejvmsks'
  })
}
