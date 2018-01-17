import postRule from './post'
import authRule from './auth'
import collecRule from './collection'
import searchRule from './search'
// import groupRule from './group'
export default function mockData(mockAdapter) {
  authRule(mockAdapter);
  postRule(mockAdapter);
  collecRule(mockAdapter);
  searchRule(mockAdapter);
// groupRule(mockAdapter);
}