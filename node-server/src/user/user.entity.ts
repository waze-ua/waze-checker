import { Column, Entity, PrimaryGeneratedColumn } from 'typeorm';

@Entity({ name: 'user' })
export class UserEntity {
  @PrimaryGeneratedColumn()
  id: number;

  @Column('integer', { default: 0 })
  rank: number;

  @Column('varchar', { default: '' })
  userName: string;

  @Column('bigint', { default: 0 })
  firstEdit: number;

  @Column('bigint', { default: 0 })
  lastEdit: number;
}
