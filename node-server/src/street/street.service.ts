import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { StreetEntity } from './street.entity';
import { StreetsResponseInterface } from './types/streetsResponse.interface';

@Injectable()
export class StreetService {
  constructor(
    @InjectRepository(StreetEntity)
    private readonly streetRepository: Repository<StreetEntity>,
  ) {}

  findAll(): Promise<StreetEntity[]> {
    return this.streetRepository.find();
  }

  findById(id: number): Promise<StreetEntity> {
    return this.streetRepository.findOneBy({ id });
  }

  buildResponse(streets: StreetEntity[] | StreetEntity): StreetsResponseInterface {
    return {
      streets,
    };
  }
}
