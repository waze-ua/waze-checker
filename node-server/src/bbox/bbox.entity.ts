import {
  Column,
  Entity,
  JoinColumn,
  ManyToOne,
  PrimaryGeneratedColumn,
} from 'typeorm';
import { RegionEntity } from 'src/region/region.entity';

@Entity({ name: 'bbox' })
export class BboxEntity {
  @PrimaryGeneratedColumn()
  id: number;

  @Column('decimal', { precision: 10, scale: 8, default: 0 })
  south: number;

  @Column('decimal', { precision: 11, scale: 8, default: 0 })
  west: number;

  @Column('decimal', { precision: 10, scale: 8, default: 0 })
  north: number;

  @Column('decimal', { precision: 11, scale: 8, default: 0 })
  east: number;

  @ManyToOne(() => RegionEntity, (region) => region.bboxes, {
    eager: true,
    onDelete: 'CASCADE',
  })
  @JoinColumn({ name: 'region' })
  region: RegionEntity;
}
