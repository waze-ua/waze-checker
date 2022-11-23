import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { SegmentEntity } from './segment.entity';
import { SegmentsResponseInterface } from './types/segmentsResponse.interface';

@Injectable()
export class SegmentService {
  constructor(
    @InjectRepository(SegmentEntity)
    private readonly segmentRepository: Repository<SegmentEntity>,
  ) {}

  findAll(): Promise<SegmentEntity[]> {
    return this.segmentRepository.find();
  }

  findById(id: number): Promise<SegmentEntity> {
    return this.segmentRepository.findOneBy({ id });
  }

  buildResponse(
    segments: SegmentEntity[] | SegmentEntity,
  ): SegmentsResponseInterface {
    return {
      segments,
    };
  }
}
