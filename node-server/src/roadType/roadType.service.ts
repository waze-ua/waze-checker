import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { RoadTypeEntity } from './roadType.entity';
import { RoadTypesResponseInterface } from './types/roadTypesResponse.interface';

@Injectable()
export class RoadTypeService {
  constructor(
    @InjectRepository(RoadTypeEntity)
    private readonly roadTypeRepository: Repository<RoadTypeEntity>,
  ) {}

  findAll(): Promise<RoadTypeEntity[]> {
    return this.roadTypeRepository.find();
  }

  findById(id: number): Promise<RoadTypeEntity> {
    return this.roadTypeRepository.findOneBy({ id });
  }

  buildResponse(roadTypes: RoadTypeEntity[] | RoadTypeEntity): RoadTypesResponseInterface {
    return {
      roadTypes,
    };
  }
}
