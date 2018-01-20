import postRule from './post'
import articleRule from './article'
import replyRule from './reply'
import authRule from './auth'
import collecRule from './collection'
import searchRule from './search'

// import groupRule from './group'
export default function mockData(mockAdapter) {
  replyRule(mockAdapter);
  articleRule(mockAdapter);
  authRule(mockAdapter);
  postRule(mockAdapter);
  collecRule(mockAdapter);
  searchRule(mockAdapter);
// groupRule(mockAdapter);
}