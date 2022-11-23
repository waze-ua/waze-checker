import { Column, Entity, PrimaryGeneratedColumn } from 'typeorm';

@Entity({ name: 'segment' })
export class SegmentEntity {
  @PrimaryGeneratedColumn()
  id: number;

  @Column('smallint', { default: 0 })
  allowNoDirection: number;

  @Column('integer', { default: 0 })
  createdBy: number;

  @Column('bigint', { default: 0 })
  createdOn: number;

  @Column('smallint', { default: 0 })
  flags: number;

  @Column('integer', { default: 0 })
  fromNodeId: number;

  @Column('smallint', { default: 0 })
  fwdDirection: number;

  @Column('smallint', { default: 0 })
  fwdFlags: number;

  @Column('smallint', { default: 0 })
  fwdMaxSpeed: number;

  @Column('smallint', { default: 0 })
  fwdMaxSpeedUnverified: number;

  @Column('smallint', { default: 0 })
  fwdToll: number;

  @Column('smallint', { default: 0 })
  fwdTurnsLocked: number;

  @Column('smallint', { default: 0 })
  hasClosures: number;

  @Column('smallint', { default: 0 })
  hasHNs: number;

  @Column('smallint', { default: 0 })
  length: number;

  @Column('smallint', { default: 0 })
  level: number;

  @Column('smallint', { default: 0 })
  lockRank: number;

  @Column('integer', { default: 0 })
  street: number;

  @Column('smallint', { default: 0 })
  rank: number;

  @Column('smallint', { default: 0 })
  revDirection: number;

  @Column('smallint', { default: 0 })
  revFlags: number;

  @Column('smallint', { default: 0 })
  revMaxSpeed: number;

  @Column('smallint', { default: 0 })
  revMaxSpeedUnverified: number;

  @Column('smallint', { default: 0 })
  revToll: number;

  @Column('smallint', { default: 0 })
  revTurnsLocked: number;

  @Column('smallint', { default: 0 })
  roadType: number;

  @Column('smallint', { default: 0 })
  routingRoadType: number;

  @Column('smallint', { default: 0 })
  separator: number;

  @Column('integer', { default: 0 })
  toNodeId: number;

  @Column('integer', { default: 0 })
  updatedBy: number;

  @Column('bigint', { default: 0 })
  updatedOn: number;

  @Column('smallint', { default: 0 })
  validated: number;

  @Column('linestring', { default: null })
  coordinates: number;

  @Column('decimal', { precision: 11, scale: 8, default: 0 })
  lon: number;

  @Column('decimal', { precision: 10, scale: 8, default: 0 })
  lat: number;

  @Column('smallint', { default: 0 })
  hasTransition: number;

  @Column('varchar', { default: ''})
  startPoint: number;

  @Column('varchar', { default: ''})
  endPoint: number;

  @Column('integer', { default: 0 })
  region: number;

  @Column('smallint', { default: 0 })
  notConnected: number;

  @Column('smallint', { default: 0 })
  withoutTurns: number;

  @Column('smallint', { default: 0 })
  hasIntersection: number;
}
