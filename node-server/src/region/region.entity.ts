import { Column, Entity, OneToMany, PrimaryGeneratedColumn } from 'typeorm';
import { BboxEntity } from 'src/bbox/bbox.entity';

@Entity({ name: 'region' })
export class RegionEntity {
  @PrimaryGeneratedColumn()
  id: number;

  @Column()
  name: string;

  @Column('geometry')
  polygon: string;

  @Column('bigint')
  lastUpdate: number;

  @OneToMany(() => BboxEntity, (bbox) => bbox.region)
    bboxes: BboxEntity[]
}
