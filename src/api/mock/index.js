import postRule from './post'
import authRule from './auth'
// import groupRule from './group'
export default function mockData(mockAdapter) {
  authRule(mockAdapter);
  postRule(mockAdapter);
// groupRule(mockAdapter);
}