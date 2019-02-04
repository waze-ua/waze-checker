import { helper } from '@ember/component/helper';

export function lockRank([rank]) {
  const lockRanks = [
    { id: 0, name: 'auto (1)' },
    { id: 1, name: '1' },
    { id: 2, name: '2' },
    { id: 3, name: '3' },
    { id: 4, name: '4' },
    { id: 5, name: '5' },
    { id: 6, name: '6' },
  ];

  let item = lockRanks.find(item => {
    return item.id === rank;
  });
  return item.name;
}

export default helper(lockRank);
