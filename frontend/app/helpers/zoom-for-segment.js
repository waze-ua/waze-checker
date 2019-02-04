import { helper } from '@ember/component/helper';

export function zoomForSegment([roadType]) {
  const roadTypes = [2, 3, 4, 5, 7];
  if (roadTypes.includes(+roadType)) {
    return 3;
  }
  return 5;
}

export default helper(zoomForSegment);
