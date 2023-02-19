import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { KeyValueEntity } from './keyValue.entity';
import { KeyValueResponseInterface } from './types/keyValueResponse.interface';

@Injectable()
export class KeyValueService {
  constructor(
    @InjectRepository(KeyValueEntity)
    private readonly keyValueRepository: Repository<KeyValueEntity>,
  ) {}

  findByKey(key: string): Promise<KeyValueEntity> {
    return this.keyValueRepository.findOneBy({ key });
  }

  async setValue(key: string, value: string): Promise<KeyValueEntity> {
    const result = await this.keyValueRepository.findOneBy({ key });

    if (result) {
      await this.keyValueRepository.update({ key }, { value });
    } else {
      await this.keyValueRepository.insert({ key, value });
    }

    return this.keyValueRepository.findOneBy({ key });
  }

  buildResponse(keyValue: KeyValueEntity): KeyValueResponseInterface {
    return {
      keyValue,
    };
  }
}
