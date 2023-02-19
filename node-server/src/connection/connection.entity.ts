import { Column, Entity, PrimaryGeneratedColumn } from 'typeorm';

@Entity({ name: 'connection' })
export class ConnectionEntity {
  @PrimaryGeneratedColumn()
  id: number;

  @Column('integer', { default: 0 })
  fromSegment: number;

  @Column('integer', { default: 0 })
  toSegment: number;

  @Column('smallint', { default: 0 })
  direction: number;

  @Column('smallint', { default: 0 })
  isAllowed: number;
}
