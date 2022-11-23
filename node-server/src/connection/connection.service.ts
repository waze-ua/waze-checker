import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { ConnectionEntity } from './connection.entity';
import { ConnectionsResponseInterface } from './types/connectionsResponse.interface';

@Injectable()
export class ConnectionService {
  constructor(
    @InjectRepository(ConnectionEntity)
    private readonly connectionRepository: Repository<ConnectionEntity>,
  ) {}

  findAll(): Promise<ConnectionEntity[]> {
    return this.connectionRepository.find();
  }

  findById(id: number): Promise<ConnectionEntity> {
    return this.connectionRepository.findOneBy({ id });
  }

  buildResponse(
    connections: ConnectionEntity[] | ConnectionEntity,
  ): ConnectionsResponseInterface {
    return {
      connections,
    };
  }
}
