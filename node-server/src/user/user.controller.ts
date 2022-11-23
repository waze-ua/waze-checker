import { Controller, Get, Param } from '@nestjs/common';
import { UserService } from './user.service';
import { UsersResponseInterface } from './types/usersResponse.interface';

@Controller('users')
export class UserController {
  constructor(private readonly userService: UserService) {}

  @Get()
  async findAll(): Promise<UsersResponseInterface> {
    const regions = await this.userService.findAll();

    return this.userService.buildResponse(regions);
  }

  @Get(':id')
  async findRecord(@Param('id') id: number): Promise<UsersResponseInterface> {
    const user = await this.userService.findById(id);

    return this.userService.buildResponse(user);
  }

}
