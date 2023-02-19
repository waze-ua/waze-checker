import { Column, Entity, PrimaryColumn } from 'typeorm';

@Entity({ name: 'key_value' })
export class KeyValueEntity {
  @PrimaryColumn()
  key: string;

  @Column('text')
  value: string;
}
