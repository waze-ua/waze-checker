import { helper } from '@ember/component/helper';

export function zoomForSegment([length]) {

  if (+length > 5000) {
    return 1;
  }

  if (+length > 3000) {
    return 2;
  }

  if (+length > 1000) {
    return 3;
  }

  if (+length > 300) {
    return 4;
  }

  if (+length > 150) {
    return 5;
  }
  
  return 6;
  
}

export default helper(zoomForSegment);
