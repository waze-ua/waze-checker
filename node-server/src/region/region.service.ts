import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { RegionEntity } from './region.entity';
import { RegionsResponseInterface } from './types/regionsResponse.interface';

@Injectable()
export class RegionService {
  constructor(
    @InjectRepository(RegionEntity)
    private readonly regionRepository: Repository<RegionEntity>,
  ) {}

  findAll(): Promise<RegionEntity[]> {
    return this.regionRepository.find();
  }

  findById(id: number): Promise<RegionEntity> {
    return this.regionRepository.findOneBy({ id });
  }

  buildResponse(
    regions: RegionEntity[] | RegionEntity,
  ): RegionsResponseInterface {
    return {
      regions,
    };
  }
}
