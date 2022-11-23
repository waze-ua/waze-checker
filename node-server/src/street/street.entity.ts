import { Column, Entity, PrimaryGeneratedColumn } from 'typeorm';

@Entity({ name: 'street' })
export class StreetEntity {
  @PrimaryGeneratedColumn()
  id: number;

  @Column('varchar', { default: '' })
  name: string;

  @Column('integer', { default: 0 })
  city: number;

  @Column('integer', { default: 0 })
  isEmpty: number;
}
