export default function articleRule(mockAdapter) {
  mockAdapter
    .onGet(/articles\/\d+/)
    .reply(200, {
      id: 1,
      title: '通过接口编辑',
      content: '成功',
      update_at: '2017-07-20T12:50:30.176Z',
      create_at: '2017-07-20T12:50:30.176Z',
      lock: false,
      approved: false,
      approved_num: 1,
      collected: false,
      collected_num: 1,
      author: {
        id: '192',
        name: 'xiaochao_php',
        avatar_url:
          'http://7xlx4u.com1.z0.glb.clouddn.com/o_1b34pfog9v161kdlkkm1kt41f697.jpg?imageView2/1/w/100/h/100',
        articles_num: 154
      }
    })
    .onPost('/articles')
    .reply(200, {
      id: 410
    })
    .onDelete(/\/articles\/\d+\/lock/).reply(204)
    .onPost(/\/articles\/\d+\/lock/).reply(204)
    .onPost(/\/articles\/\d+\/approval/).reply(204)
    .onDelete(/\/articles\/\d+\/approval/).reply(204)
    .onPut(/\/users\/\d+\/collections/).reply(204)
    .onDelete(/\/users\/\d+\/collections/).reply(204)
}
