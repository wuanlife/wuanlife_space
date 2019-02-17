
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
  .onPost('/api/users/login').reply(200, {
    'ID-Token': 'bababababa.eyJpZCI6IjE5MiIsIm5hbWUiOiIxMTEiLCJtYWlsIjoiNDQzNDc0NzEzQHFxLmNvbSJ9.dadadadada'
  })
  .onPost('/api/users/register').reply(200, {
    'ID-Token': 'bababababa.eyJpZCI6IjE5MiIsIm5hbWUiOiIxMTEiLCJtYWlsIjoiNDQzNDc0NzEzQHFxLmNvbSJ9.dadadadada'
  })
  .onPost('/api/auth').reply(200, {
    'Access-Token': 'dsjkvbeivleavmkmvksdnboifejvmsks'
  })
}
