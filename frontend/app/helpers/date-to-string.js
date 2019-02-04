import { helper } from '@ember/component/helper';

export function dateToString([date]) {
  return new Date(+date).toLocaleString();
}

export default helper(dateToString);
