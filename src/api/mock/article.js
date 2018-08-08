export default function articleRule(mockAdapter) {
  mockAdapter
    .onGet(/articles\/\d+/)
    .reply(200, {
      id: '1',
      title: '通过接口编辑',
      content: '成功',
      update_at: '2017-07-20T12:50:30.176Z',
      create_at: '2017-07-20T12:50:30.176Z',
      lock: true,
      approved: false,
      approved_num: 0,
      collected: true,
      collected_num: 0,
      author: {
        id: '58',
        name: 'xiaochao_php',
        avatar_url:
          'http://7xlx4u.com1.z0.glb.clouddn.com/o_1b34pfog9v161kdlkkm1kt41f697.jpg?imageView2/1/w/100/h/100',
        articles_num: '154'
      }
    })
    .onPost('/articles')
    .reply(200, {
      id: 410
    });
}
