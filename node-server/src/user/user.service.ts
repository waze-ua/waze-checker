import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { UserEntity } from './user.entity';
import { UsersResponseInterface } from './types/usersResponse.interface';

@Injectable()
export class UserService {
  constructor(
    @InjectRepository(UserEntity)
    private readonly userRepository: Repository<UserEntity>,
  ) {}

  findAll(): Promise<UserEntity[]> {
    return this.userRepository.find();
  }

  findById(id: number): Promise<UserEntity> {
    return this.userRepository.findOneBy({ id });
  }

  buildResponse(users: UserEntity[] | UserEntity): UsersResponseInterface {
    return {
      users,
    };
  }
}
