import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { BboxEntity } from './bbox.entity';
import { BboxesResponseInterface } from './types/bboxesResponse.interface';

@Injectable()
export class BboxService {
  constructor(
    @InjectRepository(BboxEntity)
    private readonly bboxRepository: Repository<BboxEntity>,
  ) {}

  findAll(): Promise<BboxEntity[]> {
    return this.bboxRepository.find();
  }

  findById(id: number): Promise<BboxEntity> {
    return this.bboxRepository.findOneBy({ id });
  }

  buildResponse(bboxes: BboxEntity[] | BboxEntity): BboxesResponseInterface {
    return {
      bboxes,
    };
  }
}
