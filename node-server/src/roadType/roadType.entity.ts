import { Column, Entity, PrimaryGeneratedColumn } from 'typeorm';

@Entity({ name: 'road_type' })
export class RoadTypeEntity {
  @PrimaryGeneratedColumn()
  id: number;

  @Column('varchar', { default: '' })
  name: string;
}
